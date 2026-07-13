<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Unit\FileStore;

use AnthonyEdmonds\LaravelFileStore\FileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\Classes\MyFileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\TestCase;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadTest extends TestCase
{
    protected FileStore $fileStore;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileStore = new MyFileStore();
        $this->fileStore->files = [
            'juice' => $this->makeFile(),
        ];
    }

    public function test(): void
    {
        $this->assertInstanceOf(
            StreamedResponse::class,
            $this->fileStore->download('juice'),
        );
    }
}
