<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Unit\FileStore;

use AnthonyEdmonds\LaravelFileStore\FileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\Classes\MyFileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\Classes\MyModel;
use AnthonyEdmonds\LaravelFileStore\Tests\TestCase;

class ToArrayTest extends TestCase
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

    public function test(): void
    {
        $this->assertEquals(
            $this->files,
            $this->fileStore->toArray(),
        );
    }
}
