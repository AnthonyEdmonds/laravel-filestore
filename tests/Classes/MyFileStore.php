<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Classes;

use AnthonyEdmonds\LaravelFileStore\FileStore;

class MyFileStore extends FileStore
{
    public function downloadRoute(string $hash, array $routeParameters = []): string
    {
        return "https://download/$hash";
    }

    public function removeRoute(string $hash, array $routeParameters = []): string
    {
        return "https://remove/$hash";
    }
}
