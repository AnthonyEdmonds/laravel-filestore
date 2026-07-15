<?php

namespace AnthonyEdmonds\LaravelFileStore;

use BackedEnum;
use Countable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use JsonSerializable;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class FileStore implements Arrayable, CastsAttributes, JsonSerializable, Countable
{
    /** @var File[] */
    public array $files = [];

    public ?Model $model = null;

    public bool $hasKey = false;

    // Setup
    final public function __construct()
    {
        //
    }

    public function setup(
        Model $model,
        array $existingFiles = [],
    ): static {
        $this->model = $model;

        $this->hasKey = $this->model->getKey() !== null;

        $this->model::saving(function () {
            $this->applyRemoves();
        });

        $this->model::saved(function () {
            $this->applyAdds();
        });

        foreach ($existingFiles as $hash => $file) {
            $this->files[$hash] = new File(
                $this,
                $hash,
                $file['name'],
                $file['size'] ?? '-',
                $file['stored'] ?? true,
                $file['remove'] ?? false,
            );
        }

        return $this;
    }

    // Arrayable
    public function toArray(): array
    {
        $files = [];

        foreach ($this->files as $hash => $file) {
            $files[$hash] = $file->toArray();
        }

        return $files;
    }

    // CastsAttributes
    public function get(Model $model, string $key, mixed $value, array $attributes): static
    {
        if (is_string($value) === false) {
            $value = '[]';
        }

        if (json_validate($value) === false) {
            throw new InvalidFileStore("The FileStore contained in \"$key\" could not be parsed as JSON");
        }

        return $this->setup(
            $model,
            json_decode($value, true),
        );
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        return match (true) {
            is_string($value) => $value,
            is_array($value) => $this->setup($model, $value)->toJson(),
            $value instanceof FileStore => $value->toJson(),
            default => throw new InvalidFileStore("The value passed to \"$key\" was not a valid FileStore"),
        };
    }

    // JsonSerializable
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    // Countable
    public function count(): int
    {
        $count = 0;

        foreach ($this->files as $file) {
            if ($file->remove === false) {
                ++$count;
            }
        }

        return $count;
    }

    public function countString(): string
    {
        return Str::plural('file', $this->count(), true);
    }

    // Max files
    /** @returns int The maximum number of files allowed in this FileStore */
    public function maxFiles(): int
    {
        return 0;
    }

    public function maxFilesString(): string
    {
        return Str::plural('file', $this->maxFiles(), true);
    }

    // Permissions
    /** @returns BackedEnum|string|null The permission used to control access to the filestore  */
    public function permission(): BackedEnum|string|null
    {
        return null;
    }

    // File types
    /** @returns array The types of files allowed in ".jpg" format */
    public function allowedMimes(): array
    {
        return [];
    }

    public function allowedMimesFormatted(): array
    {
        $mimes = [];
        $allowedMimes = $this->allowedMimes();

        foreach ($allowedMimes as $mime) {
            $mimes[] = str_replace('.', '', $mime);
        }

        return $mimes;
    }

    public function allowedMimesString(): string
    {
        $allowedMimes = $this->allowedMimes();

        $finalGlue = count($allowedMimes) > 2
            ? ', or '
            : ' or ';

        return Arr::join($allowedMimes, ', ', $finalGlue);
    }

    // File size
    /** @returns int The maximum filesize allowed in bytes */
    public function maxFileSize(): int
    {
        return 0;
    }

    public function maxFileSizeString(): string
    {
        return Number::fileSize($this->maxFileSize(), 2);
    }

    // Store size
    /** @returns int The maximum number of files allowed in this FileStore */
    public function currentStoreSize(): int
    {
        $size = 0;

        foreach ($this->files as $file) {
            $size += $file->realSize();
        }

        return $size;
    }

    public function currentStoreSizeString(): string
    {
        return Number::fileSize($this->currentStoreSize(), 2);
    }

    /** @returns int The maximum overall FileStore filesize allowed in bytes */
    public function maxStoreSize(): int
    {
        return 0;
    }

    public function maxStoreSizeString(): string
    {
        return Number::fileSize($this->maxStoreSize(), 2);
    }

    // Files
    public function add(UploadedFile $file): File
    {
        $file = File::create($this, $file);
        $this->files[$file->hash] = $file;

        uasort($this->files, function (File $source, File $comparator) {
            return $source->name <=> $comparator->name;
        });

        return $file;
    }

    public function download(string $hash): StreamedResponse
    {
        return $this->files[$hash]->download();
    }

    public function list(bool $showRemoved = false): array
    {
        $list = [];

        foreach ($this->files as $hash => $file) {
            if (
                $showRemoved === false
                && $file->remove === true
            ) {
                continue;
            }

            $list[] = [
                'download_url' => $this->downloadRoute($hash),
                'name' => $file->name,
                'remove_url' => $this->removeRoute($hash),
                'size' => $file->size,
            ];
        }

        return $list;
    }

    public function remove(string $hash): File
    {
        $this->files[$hash]->markForRemoval();
        return $this->files[$hash];
    }

    public function applyAdds(): void
    {
        foreach ($this->files as $file) {
            if ($file->stored === false) {
                $file->store();
            }
        }
    }

    public function applyRemoves(): void
    {
        foreach ($this->files as $hash => $file) {
            if ($file->remove === true) {
                $file->remove();
                unset($this->files[$hash]);
            }
        }
    }

    // Disks
    /** The name of the filesystem to use for permanently storing files */
    abstract public function storeDiskName(): string;

    /** The name of the filesystem to use for temporarily storing files */
    abstract public function tempDiskName(): string;

    public function storeDisk(): Filesystem
    {
        return Storage::disk($this->storeDiskName());
    }

    public function tempDisk(): Filesystem
    {
        return Storage::disk($this->tempDiskName());
    }

    // Routing
    /** The `route()` users can download files using */
    abstract public function downloadRoute(string $hash): string;

    /** The `route()` users can remove files using */
    abstract public function removeRoute(string $hash): string;
}
