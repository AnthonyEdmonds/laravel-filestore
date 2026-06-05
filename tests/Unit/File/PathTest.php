<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Unit\File;

use AnthonyEdmonds\LaravelFileStore\File;
use AnthonyEdmonds\LaravelFileStore\Tests\TestCase;

class PathTest extends TestCase
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
            "{$this->model->id}/{$this->file->hash}",
            $this->file->path(),
        );
    }
}
