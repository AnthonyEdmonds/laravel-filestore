<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Unit\File;

use AnthonyEdmonds\LaravelFileStore\File;
use AnthonyEdmonds\LaravelFileStore\Tests\TestCase;

class RealSizeTest extends TestCase
{
    protected File $file;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();

        $this->file = $this->makeFile();
        $this->model->id = 1;
    }

    public function testStored(): void
    {
        $this->file->store();

        $this->assertEquals(
            57366,
            $this->file->realSize(),
        );
    }

    public function testTemp(): void
    {
        $this->file->stored = false;

        $this->assertEquals(
            57366,
            $this->file->realSize(),
        );
    }
}
