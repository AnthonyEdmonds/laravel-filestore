![Composer status](.github/composer.svg)
![Coverage status](.github/coverage.svg)
![Laravel version](.github/laravel.svg)
![NPM status](.github/npm.svg)
![PHP version](.github/php.svg)
![Tests status](.github/tests.svg)

# Laravel FileStore

Manage adding and removing files on your `Model`, only persisting the changes on save!

## What is this?

Forms with files which allow the user to draft changes before committing to them, need a way to manage the uploading and removal of files only once the user commits to them.

This library adds a `FileStore` cast which automatically handles these changes:

1. User uploads a file; this is added to the temp disk
2. User removes an existing file; this is marked for removal
3. User saves the `Model`; this moves temp files to store, and removes marked files from store

The underlying `FileStore` object is saved as a JSON string, which can be stored in a text column.

The `FileStore` class provides all the methods you need to add, remove, and list your files.

Your `Model` can have one `FileStore` per attribute for complete compartmentalisation.

Files are stored as `$hash` on the temporary disk, and `$model->id/$hash` on the store disk.

## Installation

You can install this library using Composer:

```bash
composer require anthonyedmonds/laravel-filestore
```

## How to use

1. Create a new `FileStore` class
   ```php
   class MyFileStore extends FileStore
   {
       ...
   }
   ```
2. Add the `FileStore` to your model
   ```php
   class MyModel extends Model
   {
       ...
       protected $casts = [
           'file_column' => MyFileStore::class,
       ];
       ...
   }
   ``` 
3. Tie your system into the `FileStore` ecosystem, such as through controllers
   ```php
    class MyController extends Controller
   {
       public function download(MyModel $model, string $hash): StreamedResponse
       {
           return $model->file_column->download($hash);
       }
   
       public function index(MyModel $model): View
       {
           return view('my-view', [
               'files' => $model->file_column->list(),
           ]);
       }
   
       public function remove(MyModel $model, string $hash): RedirectResponse
       {
           $model->file_column->remove($hash);
           ...
       }
   
       public function save(MyModel $model): RedirectResponse
       {
           // Added and removed files will be processed when you call `save()` on the Model
           $model->save();
           ...
       }
   
       public function upload(FileRequest $formRequest, MyModel $model): RedirectResponse
       {
           $model->file_column->add(
               $formRequest->file('uploaded_file'),
           );
           ...
       }
   }
   ```

## Roadmap

* Self-healing filestore

## Help and support

You are welcome to raise any issues or questions on GitHub.

If you wish to contribute to this library, raise an issue before submitting a forked pull request.

## Licence

Published under the MIT licence.
