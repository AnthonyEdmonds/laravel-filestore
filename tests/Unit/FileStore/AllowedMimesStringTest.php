<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Unit\FileStore;

use AnthonyEdmonds\LaravelFileStore\FileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\Classes\MyFileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\Classes\ValidatedFileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\TestCase;

class AllowedMimesStringTest extends TestCase
{
    protected FileStore $fileStore;

    public function testEmpty(): void
    {
        $this->fileStore = new MyFileStore();

        $this->assertEquals(
            '',
            $this->fileStore->allowedMimesString(),
        );
    }

    public function testTwo(): void
    {
        $this->fileStore = new ValidatedFileStore();
        $this->fileStore->allowedMimes = [
            '.xlsx',
            '.bat',
        ];

        $this->assertEquals(
            '.xlsx or .bat',
            $this->fileStore->allowedMimesString(),
        );
    }

    public function testMultiple(): void
    {
        $this->fileStore = new ValidatedFileStore();

        $this->assertEquals(
            '.xlsx, .png, or .bat',
            $this->fileStore->allowedMimesString(),
        );
    }
}
