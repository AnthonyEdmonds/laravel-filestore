<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Unit\File;

use AnthonyEdmonds\LaravelFileStore\File;
use AnthonyEdmonds\LaravelFileStore\Tests\Classes\MyModel;
use AnthonyEdmonds\LaravelFileStore\Tests\TestCase;
use Illuminate\Http\UploadedFile;

class CreateTest extends TestCase
{
    protected File $file;

    protected MyModel $model;

    protected UploadedFile $upload;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new MyModel();

        $this->upload = new UploadedFile(
            __DIR__ . '/../../Files/snowy.jpg',
            'snowy.jpg',
        );

        $this->file = File::create($this->model->files, $this->upload);
    }

    public function test(): void
    {
        $this->assertEquals(
            $this->upload->hashName(),
            $this->file->hash,
        );
    }
}
