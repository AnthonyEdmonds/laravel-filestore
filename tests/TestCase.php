<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests;

use AnthonyEdmonds\LaravelFileStore\File;
use AnthonyEdmonds\LaravelFileStore\Tests\Classes\MyModel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected MyModel $model;

    protected UploadedFile $upload;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('temp');
        Storage::fake('store');
    }

    public function makeFile(): File
    {
        $this->model = new MyModel();

        $this->upload = new UploadedFile(
            __DIR__ . '/Files/snowy.jpg',
            'snowy.jpg',
        );

        return File::create($this->model->files, $this->upload);
    }
}
