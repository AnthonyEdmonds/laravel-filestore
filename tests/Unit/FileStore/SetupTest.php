<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Unit\FileStore;

use AnthonyEdmonds\LaravelFileStore\File;
use AnthonyEdmonds\LaravelFileStore\FileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\Classes\MyModel;
use AnthonyEdmonds\LaravelFileStore\Tests\TestCase;

class SetupTest extends TestCase
{
    protected FileStore $fileStore;

    protected MyModel $model;

    protected array $files = [
        'my-hash' => [
            'name' => 'My name',
            'size' => '12 mB',
            'stored' => true,
            'remove' => false,
        ],
        'other-hash' => [
            'name' => 'Other name',
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->useDatabase();

        $this->model = new MyModel();
        $this->model->files = $this->files;
        $this->fileStore = $this->model->files;
        $this->model->save();
    }

    public function test(): void
    {
        $this->assertTrue(
            $this->fileStore->model->is($this->model),
        );

        $this->assertEquals(
            new File(
                $this->fileStore,
                'my-hash',
                'My name',
                '12 mB',
                true,
                false,
            ),
            $this->fileStore->files['my-hash'],
        );

        $this->assertEquals(
            new File(
                $this->fileStore,
                'other-hash',
                'Other name',
                '-',
                true,
                false,
            ),
            $this->fileStore->files['other-hash'],
        );

        $this->assertTrue(
            MyModel::getEventDispatcher()->hasListeners('eloquent.saving: ' . MyModel::class),
        );

        $this->assertTrue(
            MyModel::getEventDispatcher()->hasListeners('eloquent.saved: ' . MyModel::class),
        );
    }
}
