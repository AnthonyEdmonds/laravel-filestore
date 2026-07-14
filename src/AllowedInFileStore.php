<?php

namespace AnthonyEdmonds\LaravelFileStore;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Concerns\ValidatesAttributes;

class AllowedInFileStore implements ValidationRule
{
    use ValidatesAttributes;

    public FileStore $fileStore;

    /** @param FileStore|class-string<FileStore> $fileStore */
    public function __construct(
        FileStore|string $fileStore,
    ) {
        $this->fileStore = is_string($fileStore) === true
            ? new $fileStore()
            : $fileStore;
    }

    /** @param UploadedFile $value */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->validateFile($attribute, $value) === false) {
            $fail('The selected file could not be uploaded; try again');
            return;
        }

        $allowedMimes = $this->fileStore->allowedMimesFormatted();

        if (
            empty($allowedMimes) === false
            && $this->validateMimes($attribute, $value, $allowedMimes) === false
        ) {
            $allowedMimesString = $this->fileStore->allowedMimesString();
            $fail("The selected file must be an $allowedMimesString");
            return;
        }

        $maxFileSize = $this->fileStore->maxFileSize();

        if (
            $maxFileSize > 0
            && $value->getSize() > $maxFileSize
        ) {
            $maxFileSizeString = $this->fileStore->maxFileSizeString();
            $fail("The selected file must be smaller than $maxFileSizeString");
            return;
        }

        if ($this->fileStore->model !== null) {
            $maxFiles = $this->fileStore->maxFiles();

            if (
                $maxFiles > 0
                && $this->fileStore->count() >= $maxFiles
            ) {
                $maxFilesString = $this->fileStore->maxFilesString();
                $fail("You can only upload up to $maxFilesString files");
                return;
            }

            $maxStoreSize = $this->fileStore->maxStoreSize();

            if ($maxStoreSize > 0) {
                $currentStoreSize = $this->fileStore->currentStoreSize();
                $uploadedFileSize = $value->getSize();

                if ($currentStoreSize + $uploadedFileSize > $maxStoreSize) {
                    $maxStoreSizeString = $this->fileStore->maxStoreSizeString();
                    $fail("You can only upload up to $maxStoreSizeString of files");
                }
            }
        }
    }
}
