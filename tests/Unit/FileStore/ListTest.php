<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Unit\FileStore;

use AnthonyEdmonds\LaravelFileStore\FileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\Classes\MyFileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\TestCase;

class ListTest extends TestCase
{
    protected FileStore $fileStore;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileStore = new MyFileStore();
        $this->fileStore->files = [
            'fresh' => $this->makeFile(),
            'juice' => $this->makeFile(),
            'now' => $this->makeFile(),
        ];

        $this->fileStore->remove('juice');
    }

    public function testWithoutRemoved(): void
    {
        $this->assertEquals(
            [
                [
                    'download_url' => $this->fileStore->downloadRoute('fresh'),
                    'name' => 'snowy.jpg',
                    'remove_url' => $this->fileStore->removeRoute('fresh'),
                    'size' => '57.37 kB',
                ],
                [
                    'download_url' => $this->fileStore->downloadRoute('now'),
                    'name' => 'snowy.jpg',
                    'remove_url' => $this->fileStore->removeRoute('now'),
                    'size' => '57.37 kB',
                ],
            ],
            $this->fileStore->list(),
        );
    }

    public function testWithRemoved(): void
    {
        $this->assertEquals(
            [
                [
                    'download_url' => $this->fileStore->downloadRoute('fresh'),
                    'name' => 'snowy.jpg',
                    'remove_url' => $this->fileStore->removeRoute('fresh'),
                    'size' => '57.37 kB',
                ],
                [
                    'download_url' => $this->fileStore->downloadRoute('juice'),
                    'name' => 'snowy.jpg',
                    'remove_url' => $this->fileStore->removeRoute('juice'),
                    'size' => '57.37 kB',
                ],
                [
                    'download_url' => $this->fileStore->downloadRoute('now'),
                    'name' => 'snowy.jpg',
                    'remove_url' => $this->fileStore->removeRoute('now'),
                    'size' => '57.37 kB',
                ],
            ],
            $this->fileStore->list(true),
        );
    }
}
