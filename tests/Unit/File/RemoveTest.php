<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Unit\File;

use AnthonyEdmonds\LaravelFileStore\File;
use AnthonyEdmonds\LaravelFileStore\Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class RemoveTest extends TestCase
{
    protected File $file;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();

        $this->file = $this->makeFile();
        $this->model->id = 1;
        $this->file->store();

        $this->file->remove();
    }

    public function test(): void
    {
        $this->assertFalse(
            Storage::disk('store')->exists(
                $this->file->path(),
            ),
        );

        $this->assertFalse(
            $this->file->stored,
        );
    }
}
