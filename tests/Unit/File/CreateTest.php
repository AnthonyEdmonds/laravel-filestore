<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Unit\File;

use AnthonyEdmonds\LaravelFileStore\File;
use AnthonyEdmonds\LaravelFileStore\FileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class CreateTest extends TestCase
{
    protected File $file;

    protected function setUp(): void
    {
        parent::setUp();

        $this->file = $this->makeFile();
    }

    public function test(): void
    {
        $this->assertInstanceOf(
            FileStore::class,
            $this->file->fileStore,
        );

        $this->assertEquals(
            $this->upload->hashName(),
            $this->file->hash,
        );

        $this->assertEquals(
            $this->upload->getClientOriginalName(),
            $this->file->name,
        );

        $this->assertEquals(
            '57.37 kB',
            $this->file->size,
        );

        $this->assertFalse(
            $this->file->stored,
        );

        $this->assertFalse(
            $this->file->remove,
        );

        $this->assertTrue(
            Storage::disk('temp')->exists($this->file->hash),
        );
    }
}
