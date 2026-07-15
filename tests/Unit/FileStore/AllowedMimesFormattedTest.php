<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Unit\FileStore;

use AnthonyEdmonds\LaravelFileStore\FileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\Classes\MyFileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\Classes\ValidatedFileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\TestCase;

class AllowedMimesFormattedTest extends TestCase
{
    protected FileStore $fileStore;

    public function testEmpty(): void
    {
        $this->fileStore = new MyFileStore();

        $this->assertEquals(
            [],
            $this->fileStore->allowedMimesFormatted(),
        );
    }

    public function testMultiple(): void
    {
        $this->fileStore = new ValidatedFileStore();

        $this->assertEquals(
            ['xlsx', 'png', 'bat'],
            $this->fileStore->allowedMimesFormatted(),
        );
    }
}
