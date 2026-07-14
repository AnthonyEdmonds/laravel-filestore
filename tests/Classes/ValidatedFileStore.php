<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Classes;

use BackedEnum;

class ValidatedFileStore extends MyFileStore
{
    public array $allowedMimes = [
        '.xlsx',
        '.png',
        '.bat',
    ];

    public int $maxFileSize = 760000;

    public function allowedMimes(): array
    {
        return $this->allowedMimes;
    }

    public function maxFileSize(): int
    {
        return $this->maxFileSize;
    }

    public function maxFiles(): int
    {
        return 3;
    }

    public function permission(): BackedEnum|string|null
    {
        return 'add-files';
    }

    public function maxStoreSize(): int
    {
        return 800000;
    }
}
