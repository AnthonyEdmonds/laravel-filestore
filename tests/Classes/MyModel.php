<?php

namespace AnthonyEdmonds\LaravelFileStore\Tests\Classes;

use Illuminate\Database\Eloquent\Model;

/**
 * @property MyFileStore $files
 * @property int $id
 */
class MyModel extends Model
{
    protected $casts = [
        'files' => MyFileStore::class,
    ];
}
