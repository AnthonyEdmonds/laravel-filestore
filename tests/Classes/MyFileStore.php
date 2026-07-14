<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Classes;

use AnthonyEdmonds\LaravelFileStore\FileStore;

class MyFileStore extends FileStore
{
    public function storeDiskName(): string
    {
        return 'store';
    }

    public function tempDiskName(): string
    {
        return 'temp';
    }

    public function downloadRoute(string $hash): string
    {
        return "https://download/$hash";
    }

    public function removeRoute(string $hash): string
    {
        return "https://remove/$hash";
    }
}
