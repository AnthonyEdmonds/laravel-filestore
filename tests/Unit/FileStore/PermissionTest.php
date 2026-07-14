<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Unit\FileStore;

use AnthonyEdmonds\LaravelFileStore\FileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\Classes\MyFileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\TestCase;

class PermissionTest extends TestCase
{
    protected FileStore $fileStore;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileStore = new MyFileStore();
    }

    public function test(): void
    {
        $this->assertNull(
            $this->fileStore->permission(),
        );
    }
}
