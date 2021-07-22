- [![Starts](https://img.shields.io/github/stars/miladimos/laravel-filemanager?style=flat&logo=github)](https://github.com/miladimos/laravel-filemanager/forks)
- [![Forks](https://img.shields.io/github/forks/miladimos/laravel-filemanager?style=flat&logo=github)](https://github.com/miladimos/laravel-filemanager/stargazers)


- [English](README-en.md)

# در حال توسعه

### برای نصب در مسیر روت پروژه خود دستور زیر را در ریشه پروژه اجرا کنید

``` php
composer require miladimos/laravel-filemanager
```

2. Open your config/app.php and add the following lines:

```php
// in providers
Miladimos\FileManager\Providers\FileManagerServiceProvider::class,

// in aliases
Miladimos\FileManager\Facades\FileManagerFacade::class,
```

3. Run the command below to install package:

```
php artisan filemanager:install
```

### Configuration ! important !

next go to the file

```php
config/filemanager.php;
```

for initialize file manager first set these confings:

1. set default storage to upload file (default is: local)
2. set base directory name for file manager (default is: filemanager/)

and run bellow command for initialize:

``` php
php artisan filemanager:init
```

[comment]: <> (### نحوه استفاده)

[comment]: <> (برای اپلود فایل:)

[comment]: <> (```)

[comment]: <> (public function store&#40;Request $request&#41;)

[comment]: <> ({   )

[comment]: <> (    // This will upload your file to the default folder of selected in config storage)

[comment]: <> (    UploadService::uploadFile&#40;$request->file&#40;'some_file'&#41;&#41;;)

[comment]: <> (    // This will upload your file to the given as second parameter path of default storage)

[comment]: <> (    UploadService::uploadFile&#40;$request->file&#40;'some_file'&#41;, 'path/to/upload'&#41;;)

[comment]: <> (    // This will upload your file to the given storage)

[comment]: <> (    UploadService::uploadFile&#40;$request->file&#40;'some_file'&#41;, 'path/to/upload', 'storage_name'&#41;;)

[comment]: <> (    // This will also resize image to the given width and height)

[comment]: <> (    UploadService::uploadFile&#40;$request->file&#40;'some_file'&#41;, 'path/to/upload', 'storage_name'&#41;;)

[comment]: <> (})

[comment]: <> (```)

[comment]: <> (برای آپلود عکس با فرمت base64:)

[comment]: <> (```php)

[comment]: <> (public function store&#40;Request $request&#41;)

[comment]: <> ({   )

[comment]: <> (    // This will upload your file to the default folder of selected in config storage)

[comment]: <> (    UploadService::uploadBase64Image&#40;$request->input&#40;'image'&#41;&#41;;)

[comment]: <> (    // This will upload your file to the given as second parameter path of default storage)

[comment]: <> (    UploadService::uploadFile&#40;$request->input&#40;'image'&#41;, 'path/to/upload'&#41;;)

[comment]: <> (    // This will upload your file to the given storage)

[comment]: <> (    UploadService::uploadFile&#40;$request->input&#40;'image'&#41;, 'path/to/upload', 'storage_name'&#41;;)

[comment]: <> (    // This will also resize image to the given width and height)

[comment]: <> (    UploadService::uploadFile&#40;$request->input&#40;'image'&#41;, 'path/to/upload', 'storage_name'&#41;;)

[comment]: <> (})

[comment]: <> (```)

### امکانات

❤️

- [x] Directory service - list, list recursive, create, delete, move
- [] File service - list, delete, move
- [] Upload service -
- [] Image service -
- [] FileGroup service -

### ویژگی ها

* شخصی سازی بالا
  
    
