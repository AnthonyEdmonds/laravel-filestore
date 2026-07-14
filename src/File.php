<?php

namespace AnthonyEdmonds\LaravelFileStore;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File as LaravelFile;
use Illuminate\Support\Number;
use Symfony\Component\HttpFoundation\StreamedResponse;

class File implements Arrayable
{
    // Setup
    public function __construct(
        public FileStore $fileStore,
        public string $hash,
        public string $name,
        public string $size,
        public bool $stored = false,
        public bool $remove = false,
    ) {
        //
    }

    public static function create(FileStore $fileStore, UploadedFile $upload): File
    {
        $file = new File(
            $fileStore,
            $upload->hashName(),
            $upload->getClientOriginalName(),
            File::size($upload->getSize()),
            false,
            false,
        );

        $file->fileStore->tempDisk()->put('/', $upload);

        return $file;
    }

    public static function size(int $size): string
    {
        return Number::fileSize($size, 2);
    }

    public function realSize(): int
    {
        return $this->stored === true
            ? $this->fileStore->storeDisk()->size(
                $this->path(),
            )
            : $this->fileStore->tempDisk()->size(
                $this->hash,
            );
    }

    // Arrayable
    public function toArray(): array
    {
        return [
            'hash' => $this->hash,
            'name' => $this->name,
            'remove' => $this->remove,
            'size' => $this->size,
            'stored' => $this->stored,
        ];
    }

    // Actions
    public function download(): StreamedResponse
    {
        return $this->stored === true
            ? $this->fileStore->storeDisk()->download(
                $this->path(),
                $this->name,
            )
            : $this->fileStore->tempDisk()->download(
                $this->hash,
                $this->name,
            );
    }

    public function markForRemoval(): File
    {
        $this->remove = true;
        return $this;
    }

    public function path(): string
    {
        $key = $this->fileStore->model->getKey();
        return "$key/$this->hash";
    }

    public function remove(): File
    {
        $this->fileStore->storeDisk()->delete(
            $this->path(),
        );

        $this->stored = false;

        return $this;
    }

    public function store(): File
    {
        $key = $this->fileStore->model->getKey();

        if ($this->fileStore->storeDisk()->exists($key) === false) {
            $this->fileStore->storeDisk()->makeDirectory($key);
        }

        $path = $this->path();
        LaravelFile::move(
            $this->fileStore->tempDisk()->path($this->hash),
            $this->fileStore->storeDisk()->path($path),
        );

        $this->stored = true;

        return $this;
    }
}
