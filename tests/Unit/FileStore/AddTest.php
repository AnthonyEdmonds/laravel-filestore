<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Unit\FileStore;

use AnthonyEdmonds\LaravelFileStore\FileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\Classes\MyFileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\TestCase;
use Illuminate\Http\UploadedFile;

class AddTest extends TestCase
{
    protected FileStore $fileStore;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileStore = new MyFileStore();

        $this->fileStore->add(
            new UploadedFile(
                __DIR__ . '/../../Files/snowy.jpg',
                'snowy.jpg',
            ),
        );

        $this->fileStore->add(
            new UploadedFile(
                __DIR__ . '/../../Files/snowy.jpg',
                'abacus.jpg',
            ),
        );

        $this->fileStore->add(
            new UploadedFile(
                __DIR__ . '/../../Files/snowy.jpg',
                'nose.jpg',
            ),
        );
    }

    public function test(): void
    {
        $this->assertEquals(
            [
                'abacus.jpg',
                'nose.jpg',
                'snowy.jpg',
            ],
            array_column(
                $this->fileStore->toArray(),
                'name',
            ),
        );
    }
}
