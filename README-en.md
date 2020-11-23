- [![Starts](https://img.shields.io/github/stars/miladimos/laravel-filemanager?style=flat&logo=github)](https://github.com/miladimos/laravel-filemanager/forks)
- [![Forks](https://img.shields.io/github/forks/miladimos/laravel-filemanager?style=flat&logo=github)](https://github.com/miladimos/laravel-filemanager/stargazers)

- [فارسی](README.md)


# laravel-file-uploads
  A package for convenient way to upload files to the different storages

### Installation

1. Run the command below to add this package:
```
composer require miladimos/laravel-filemanager
```

2. Open your config/app.php and add the following to the providers array:
```php
Miladimos\FileManager\Providers\FileManagerServiceProvider::class
```

3. Run the command below to publish the package config file config/file_uploads.php:
```
php artisan vendor:publish
```


### Configuration
Go to the file

```php
config/file_uploads.php;
```

There you have an ability to set:

1. default storage to upload file (default is: local)
2. default image quality (default is: 100)
3. default folder to put your uploads (default is: public/user-uploads)

### Usage
To upload file:

```
public function store(Request $request)
{   
    // This will upload your file to the default folder of selected in config storage
    Uploader::uploadFile($request->file('some_file'));
    
    // This will upload your file to the given as second parameter path of default storage
    Uploader::uploadFile($request->file('some_file'), 'path/to/upload');
    
    // This will upload your file to the given storage
    Uploader::uploadFile($request->file('some_file'), 'path/to/upload', 'storage_name');
    
    // This will also resize image to the given width and height
    Uploader::uploadFile($request->file('some_file'), 'path/to/upload', 'storage_name');
}
```


To upload base64 string of image:

```php
public function store(Request $request)
{   
    // This will upload your file to the default folder of selected in config storage
    Uploader::uploadBase64Image($request->input('image'));
    
    // This will upload your file to the given as second parameter path of default storage
    Uploader::uploadFile($request->input('image'), 'path/to/upload');
    
    // This will upload your file to the given storage
    Uploader::uploadFile($request->input('image'), 'path/to/upload', 'storage_name');
    
    // This will also resize image to the given width and height
    Uploader::uploadFile($request->input('image'), 'path/to/upload', 'storage_name');
}
```
