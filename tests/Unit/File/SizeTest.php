<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Unit\File;

use AnthonyEdmonds\LaravelFileStore\File;
use AnthonyEdmonds\LaravelFileStore\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\DataProvider;

class SizeTest extends TestCase
{
    #[DataProvider('expectations')]
    public function test(string $file, string $expected): void
    {
        $file = new UploadedFile(
            __DIR__ . "/../../Files/$file",
            $file,
        );

        $this->assertEquals(
            $expected,
            File::size($file->getSize()),
        );
    }

    public static function expectations(): array
    {
        return [
            [
                'file' => 'blank.txt',
                'expected' => '5.00 B',
            ],
            [
                'file' => 'snowy.jpg',
                'expected' => '56.02 KB',
            ],
            [
                'file' => 'ripley.jpg',
                'expected' => '1.03 MB',
            ],
        ];
    }
}
