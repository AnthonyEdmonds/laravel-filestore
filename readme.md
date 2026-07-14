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
2. Add the `FileStore` to your model `$casts`, specifying the disks to use for storing files permanently and temporarily
   ```php
   class MyModel extends Model
   {
       ...
       protected $casts = [
           'file_column' => MyFileStore::class . ':store,temp',
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

## Restricting the FileStore

### AllowedInFileStore validation rule

You can validate uploads destined for a `FileStore` using the `AllowedInFileStore` validation rule.

```php
public function rules(): array
{
   return [
      'my-file' => [
         'required',
         new AllowedInFileStore($this->model->my_filestore),
         // OR, if not using maxStoreSize() or maxFiles()
         new AllowedInFileStore(MyFileStore::class),
      ],
   ];
}
```

It validates the following criteria:

* Is a file
* Is an allowed mime type
* Is below the file size limit

If provided with an instance of a `FileStore` from a `Model`, it will also check:

* Would not cause the `FileStore` to exceed its file count limit
* Would not cause the `FileStore` to exceed its store size limit

### Allowed mimes

You can set a list of allowed mime types using the `allowedMimes()` method in ".jpg" format.

This is used in combination with the `AllowedInFileStore` rule to restrict files being added to the store.

The `allowedMimesString()` method lets you get the list as a string for display, with or without dots.

### Maximum file size

You can set a maximum allowed upload file size in bytes using the `maxFileSize()` method.

This is used in combination with the `AllowedInFileStore` rule to restrict files being added to the store.

The `maxFileSizeString()` method can be used to display the limit.

### Maximum store size

You can set a maximum allowed `FileStore` size in bytes using the `maxStoreSize()` method.

This is used in combination with the `AllowedInFileStore` rule to restrict adding a file which would put the `FileStore` over the limit.

The `maxStoreSizeString()` and `currentStoreSizeString()` methods may be used to display the limit.

### Max files

You can set a maximum allowed number of files in the `FileStore` using the `maxFiles()` method.

This is used in combination with the `AllowedInFileStore` rule to restrict adding too many files to a `FileStore`.

The `maxFilesString()` and `countString()` methods can be used to display the limit.

### Permissions

You can set a permission name to be used for controlling access to the `FileStore` using the `permission()` method.

No access controls are included in this library, however it could be useful for dynamic calls to `authorise()`.

## Roadmap

* Self-healing filestore
* Maximum overall filestore size

## Help and support

You are welcome to raise any issues or questions on GitHub.

If you wish to contribute to this library, raise an issue before submitting a forked pull request.

## Licence

Published under the MIT licence.
