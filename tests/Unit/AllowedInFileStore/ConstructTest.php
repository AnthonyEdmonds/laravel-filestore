<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Unit\AllowedInFileStore;

use AnthonyEdmonds\LaravelFileStore\AllowedInFileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\Classes\ValidatedFileStore;
use AnthonyEdmonds\LaravelFileStore\Tests\TestCase;

class ConstructTest extends TestCase
{
    protected AllowedInFileStore $rule;

    public function testHandlesClassString(): void
    {
        $this->rule = new AllowedInFileStore(ValidatedFileStore::class);

        $this->assertInstanceOf(
            ValidatedFileStore::class,
            $this->rule->fileStore,
        );
    }

    public function testHandlesInstance(): void
    {
        $this->rule = new AllowedInFileStore(
            new ValidatedFileStore(),
        );

        $this->assertInstanceOf(
            ValidatedFileStore::class,
            $this->rule->fileStore,
        );
    }
}
