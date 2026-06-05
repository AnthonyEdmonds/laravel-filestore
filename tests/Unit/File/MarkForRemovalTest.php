<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Unit\File;

use AnthonyEdmonds\LaravelFileStore\File;
use AnthonyEdmonds\LaravelFileStore\Tests\TestCase;

class MarkForRemovalTest extends TestCase
{
    protected File $file;

    protected function setUp(): void
    {
        parent::setUp();

        $this->file = $this->makeFile();
        $this->file->markForRemoval();
    }

    public function test(): void
    {
        $this->assertTrue(
            $this->file->remove,
        );
    }
}
