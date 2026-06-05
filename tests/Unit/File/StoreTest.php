<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Unit\File;

use AnthonyEdmonds\LaravelFileStore\File;
use AnthonyEdmonds\LaravelFileStore\Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class StoreTest extends TestCase
{
    protected File $file;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();

        $this->file = $this->makeFile();
        $this->model->id = 1;
        $this->file->store();
    }

    public function test(): void
    {
        $this->assertFalse(
            Storage::disk('temp')->exists($this->file->hash),
        );

        $this->assertTrue(
            Storage::disk('store')->exists(
                $this->file->path(),
            ),
        );

        $this->assertTrue(
            $this->file->stored,
        );
    }
}
