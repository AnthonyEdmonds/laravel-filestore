<?php

namespace AnthonyEdmonds\LaravelFileStore;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File as LaravelFile;
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
        return match (true) {
            $size > 99999 => round($size / 1000 / 1000, 2) . ' mB',
            $size > 9999 => round($size / 1000, 2) . ' kB',
            default => $size . ' B',
        };
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
