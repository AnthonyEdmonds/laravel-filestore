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
            $upload->hashName(),
            $upload->getClientOriginalName(),
            round($upload->getSize() / 1024, 2) . 'MB',
            false,
            false,
        );

        $fileStore->tempDisk()->put($file->hash, $upload);

        return $file;
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

    // Utilities
    public function download(FileStore $fileStore): StreamedResponse
    {
        return $this->stored === true
            ? $fileStore->storeDisk()->download(
                $this->path($fileStore),
            )
            : $fileStore->tempDisk()->download("$this->hash");
    }

    public function markForRemoval(): File
    {
        $this->remove = true;
        return $this;
    }

    public function path(FileStore $fileStore): string
    {
        $id = $fileStore->model->getKey();
        return "$id/$this->hash";
    }

    public function remove(FileStore $fileStore): File
    {
        $fileStore->storeDisk()->delete(
            $this->path($fileStore),
        );

        $this->stored = false;

        return $this;
    }

    public function store(FileStore $fileStore): File
    {
        LaravelFile::move(
            $fileStore->tempDisk()->path($this->hash),
            $fileStore->storeDisk()->path(
                $this->path($fileStore),
            ),
        );

        $this->stored = true;

        return $this;
    }
}
