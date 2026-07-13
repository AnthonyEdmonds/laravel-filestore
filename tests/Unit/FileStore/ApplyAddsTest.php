<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Unit\FileStore;

use AnthonyEdmonds\LaravelFileStore\FileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\Classes\MyFileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\Classes\MyModel;
use AnthonyEdmonds\LaravelFileStore\Tests\TestCase;
use Illuminate\Http\UploadedFile;

class ApplyAddsTest extends TestCase
{
    protected FileStore $fileStore;

    protected MyModel $model;

    protected UploadedFile $uploadedFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new MyModel();
        $this->model->id = 1;

        $this->uploadedFile = new UploadedFile(
            __DIR__ . '/../../Files/snowy.jpg',
            'snowy.jpg',
        );

        $this->fileStore = new MyFileStore();
        $this->fileStore->model = $this->model;
        $this->fileStore->add($this->uploadedFile);

        $this->fileStore->applyAdds();
    }

    public function test(): void
    {
        $this->assertTrue(
            $this->fileStore->files[$this->uploadedFile->hashName()]->stored,
        );
    }
}
