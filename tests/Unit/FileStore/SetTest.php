<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Unit\FileStore;

use AnthonyEdmonds\LaravelFileStore\FileStore;
use AnthonyEdmonds\LaravelFileStore\InvalidFileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\Classes\MyFileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\Classes\MyModel;
use AnthonyEdmonds\LaravelFileStore\Tests\TestCase;

class SetTest extends TestCase
{
    protected FileStore $fileStore;

    protected MyModel $model;

    protected array $files = [
        'my-hash' => [
            'hash' => 'my-hash',
            'name' => 'My name',
            'remove' => true,
            'size' => '12 mB',
            'stored' => false,
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new MyModel();

        $this->fileStore = new MyFileStore();
        $this->fileStore->setup($this->model, $this->files);
    }

    public function testFromString(): void
    {
        $this->assertEquals(
            'potato',
            $this->fileStore->set($this->model, 'files', 'potato', []),
        );
    }

    public function testFromArray(): void
    {
        $this->assertEquals(
            json_encode(
                $this->files,
            ),
            $this->fileStore->set($this->model, 'files', $this->files, []),
        );
    }

    public function testFromFileStore(): void
    {
        $this->assertEquals(
            json_encode(
                $this->files,
            ),
            $this->fileStore->set($this->model, 'files', $this->fileStore, []),
        );
    }

    public function testHandlesOtherwise(): void
    {
        $this->expectException(InvalidFileStore::class);
        $this->expectExceptionMessage('The value passed to "files" was not a valid FileStore');

        $this->fileStore->set($this->model, 'files', 12, []);
    }
}
