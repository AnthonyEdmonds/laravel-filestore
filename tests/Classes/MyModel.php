<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Classes;

use Illuminate\Database\Eloquent\Model;

/**
 * @property MyFileStore $files
 */
class MyModel extends Model
{
    protected $casts = [
        'files' => MyFileStore::class,
    ];
}
