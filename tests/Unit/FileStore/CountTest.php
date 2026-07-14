<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Unit\FileStore;

use AnthonyEdmonds\LaravelFileStore\FileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\Classes\MyFileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\Classes\MyModel;
use AnthonyEdmonds\LaravelFileStore\Tests\TestCase;

class CountTest extends TestCase
{
    protected FileStore $fileStore;

    protected MyModel $model;

    protected array $files = [
        [
            'name' => 'A',
            'remove' => false,
        ],
        [
            'name' => 'B',
            'remove' => true,
        ],
        [
            'name' => 'C',
            'remove' => false,
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new MyModel();

        $this->fileStore = new MyFileStore();
        $this->fileStore->setup($this->model, $this->files);
    }

    public function test(): void
    {
        $this->assertCount(
            2,
            $this->fileStore,
        );
    }
}
