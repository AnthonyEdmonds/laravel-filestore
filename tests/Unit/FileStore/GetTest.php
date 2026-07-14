<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Unit\FileStore;

use AnthonyEdmonds\LaravelFileStore\FileStore;
use AnthonyEdmonds\LaravelFileStore\InvalidFileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\Classes\MyFileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\Classes\MyModel;
use AnthonyEdmonds\LaravelFileStore\Tests\TestCase;

class GetTest extends TestCase
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
        $this->fileStore->setup($this->model, 'files', $this->files);
    }

    public function testHandlesNotString(): void
    {
        $this->assertInstanceOf(
            FileStore::class,
            $this->fileStore->get($this->model, 'files', [], []),
        );
    }

    public function testToFileStore(): void
    {
        $this->assertInstanceOf(
            FileStore::class,
            $this->fileStore->get(
                $this->model,
                'files',
                json_encode($this->files),
                [],
            ),
        );
    }

    public function testHandlesInvalidJson(): void
    {
        $this->expectException(InvalidFileStore::class);
        $this->expectExceptionMessage('The FileStore contained in "files" could not be parsed as JSON');

        $this->fileStore->get($this->model, 'files', '[asd]asd[]', []);
    }
}
