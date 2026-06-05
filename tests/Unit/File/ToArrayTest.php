<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Unit\File;

use AnthonyEdmonds\LaravelFileStore\File;
use AnthonyEdmonds\LaravelFileStore\Tests\TestCase;

class ToArrayTest extends TestCase
{
    protected File $file;

    protected function setUp(): void
    {
        parent::setUp();

        $this->file = $this->makeFile();
    }

    public function test(): void
    {
        $this->assertEquals(
            [
                'hash' => $this->file->hash,
                'name' => $this->file->name,
                'remove' => $this->file->remove,
                'size' => $this->file->size,
                'stored' => $this->file->stored,
            ],
            $this->file->toArray(),
        );
    }
}
