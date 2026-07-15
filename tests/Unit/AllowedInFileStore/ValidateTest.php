<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Unit\AllowedInFileStore;

use AnthonyEdmonds\LaravelFileStore\AllowedInFileStore;
use AnthonyEdmonds\LaravelFileStore\FileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\Classes\MyModel;
use AnthonyEdmonds\LaravelFileStore\Tests\Classes\ValidatedFileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\TestCase;
use Illuminate\Http\UploadedFile;

class ValidateTest extends TestCase
{
    protected AllowedInFileStore $rule;

    protected FileStore $fileStore;

    protected MyModel $model;

    protected UploadedFile $uploadedFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new MyModel();

        $this->fileStore = new ValidatedFileStore();
        $this->fileStore->setup($this->model);

        $this->uploadedFile = new UploadedFile(
            __DIR__ . '/../../Files/kerry.png',
            'kerry.png',
            test: true,
        );

        $this->rule = new AllowedInFileStore($this->fileStore);
    }

    public function testValidatesFile(): void
    {
        $this->uploadedFile = new UploadedFile(
            __DIR__ . '/../../Files/snowy.jpg',
            'snowy.jpg',
        );

        $this->assertRuleFails(
            $this->rule,
            'file',
            $this->uploadedFile,
            'The selected file could not be uploaded; try again',
        );
    }

    public function testValidatesMimes(): void
    {
        $this->uploadedFile = new UploadedFile(
            __DIR__ . '/../../Files/snowy.jpg',
            'snowy.jpg',
            test: true,
        );

        $this->assertRuleFails(
            $this->rule,
            'file',
            $this->uploadedFile,
            "The selected file must be an {$this->fileStore->allowedMimesString()}",
        );
    }

    public function testValidatesFileSize(): void
    {
        $this->fileStore->maxFileSize = 7000;

        $this->assertRuleFails(
            $this->rule,
            'file',
            $this->uploadedFile,
            "The selected file must be smaller than {$this->fileStore->maxFileSizeString()}",
        );
    }

    public function testValidatesStoreCount(): void
    {
        $this->fileStore->files = [
            $this->makeFile(),
            $this->makeFile(),
            $this->makeFile(),
        ];

        $this->assertRuleFails(
            $this->rule,
            'file',
            $this->uploadedFile,
            "You can only upload up to {$this->fileStore->maxFilesString()} files",
        );
    }

    public function testValidatesStoreSize(): void
    {
        $this->fileStore->files = [
            $this->makeFile(),
            $this->makeFile(),
        ];

        $this->assertRuleFails(
            $this->rule,
            'file',
            $this->uploadedFile,
            "You can only upload up to {$this->fileStore->maxStoreSizeString()} of files",
        );
    }

    public function testValid(): void
    {
        $this->assertRulePasses(
            $this->rule,
            'file',
            $this->uploadedFile,
        );
    }
}
