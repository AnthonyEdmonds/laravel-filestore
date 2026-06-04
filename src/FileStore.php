<?php

namespace AnthonyEdmonds\LaravelFileStore;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use JsonSerializable;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class FileStore implements Arrayable, CastsAttributes, JsonSerializable
{
    /** @var File[] */
    public array $files = [];

    public Model $model;

    // Setup
    final public function __construct(
        Model $model,
        array $existingFiles = []
    ) {
        $this->model = $model;

        $this->model::saved(function ()  {
            // TODO Keep reserve ID approach to avoid multiple calls to save?
            $this->save();
        });

        foreach ($existingFiles as $hash => $file) {
            $this->files[$hash] = new File(
                $hash,
                $file['name'],
                $file['size'] ?? '-',
                $file['stored'] ?? true,
                $file['remove'] ?? false,
            );
        }
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

        return new static(
            $model,
            json_decode($value, true),
        );
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): void
    {
        $model->setAttribute(
            $key,
            json_encode($this->toArray()),
        );
    }

    // JsonSerializable
    public function jsonSerialize(): array
    {
        return $this->toArray();
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
        return $this->files[$hash]->download($this);
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

    public function save(): void
    {
        foreach ($this->files as $hash => $file) {
            if ($file->remove === true) {
                $file->remove($this);
                unset($this->files[$hash]);

            } elseif ($file->stored === false) {
                $file->store($this);
            }
        }

        $this->model->save();
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
