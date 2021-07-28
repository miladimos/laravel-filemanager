[![Starts](https://img.shields.io/github/stars/miladimos/laravel-filemanager?style=flat&logo=github)](https://github.com/miladimos/laravel-filemanager/forks)
[![Forks](https://img.shields.io/github/forks/miladimos/laravel-filemanager?style=flat&logo=github)](https://github.com/miladimos/laravel-filemanager/stargazers)

[comment]: <> (- [English]&#40;README-en.md&#41;)

# Under Development

##### help us for development :)

### for installation in root of your project do these steps:

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

then create tables:

``` php
php artisan migrate
```

just it :)

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

### Features ❤️

#### You are free to use whatever you like 😎 ( you can just use services in your coding or use apis for your graphical file manager or whatever ...)

### Backend Services:

##### Directory service:

```php
use Miladimos\FileManager\Services\DirectoryService;

$service = new DirectoryService();
$service->createDirectory($name); // name of directory for create
$service->deleteDirectory($uuid); // uuid of directory for delete in db and disk
$service->listDirectories($path) // list all directories in given path
$service->listDirectoriesRecursive($path); // list all directories in given path Recursively
```

##### File service:

```php
use Miladimos\FileManager\Services\FileService;

$service = new FileService(); // or resolve(FileService::class)
```

##### FileGroup service:

```php
use Miladimos\FileManager\Services\FileGroupService;

$service = new FileGroupService();
$service->allFileGroups();
$service->createFileGroup(array $data); //  $data = ['title', 'description']
$service->updateFileGroup(FileGroup $fileGroup, array $data); //  $data = ['title', 'description']
$service->deleteFileGroup(FileGroup $fileGroup);
```

##### Image service:

```php
use Miladimos\FileManager\Services\ImageService;

$service = new ImageService();
```

##### Upload service:

```php
use Miladimos\FileManager\Services\UploadService;

$service = new UploadService();
```

### API over backend services:
for all requests set these headers: 

Content-Type : application/x-www-form-urlencoded

```
prefix = /api_prefix/filemanager_api_version/route_prefix

// Directories
POST   -> prefix/directories // store new directory 


// File Groups
GET    -> prefix/filegroups // return all available file groups
POST   -> prefix/filegroups // store new file groups -> receive : title, description
PUT    -> prefix/filegroups/{filegroup}/update // update file groups -> receive : title, description
DELETE -> prefix/filegroups/{filegroup} // delete file groups
```

### BACKEND TODO:

- [x] Directory service - list, list recursive, create, delete, move
- [ ] File service - list, delete, move
- [ ] Upload service -
- [ ] Image service -
- [ ] FileGroup service -
- [ ] Archive service - zip, tar

### FRONTEND TODO:

- [ ] Web view -
