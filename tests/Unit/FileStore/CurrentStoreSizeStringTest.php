<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Unit\FileStore;

use AnthonyEdmonds\LaravelFileStore\FileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\Classes\MyFileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\TestCase;

class CurrentStoreSizeStringTest extends TestCase
{
    protected FileStore $fileStore;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileStore = new MyFileStore();
        $this->fileStore->files = [
            'juice' => $this->makeFile(),
        ];
    }

    public function test(): void
    {
        $this->assertEquals(
            '56.02 KB',
            $this->fileStore->currentStoreSizeString(),
        );
    }
}
