- [![Starts](https://img.shields.io/github/stars/miladimos/laravel-filemanager?style=flat&logo=github)](https://github.com/miladimos/laravel-filemanager/forks)
- [![Forks](https://img.shields.io/github/forks/miladimos/laravel-filemanager?style=flat&logo=github)](https://github.com/miladimos/laravel-filemanager/stargazers)


- [English](README-en.md)
# در حال توسعه 

### برای نصب در مسیر روت پروژه خود دستور زیر را در ریشه پروژه اجرا کنید 

``composer require miladimos/laravel-filemanager``


2. Open your config/app.php and add the following to the providers array:
```php
Miladimos\FileManager\Providers\FileManagerServiceProvider::class
```

3. Run the command below to install package:
```
php artisan filemanager:install
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

### نحوه استفاده
برای اپلود فایل:

```
public function store(Request $request)
{   
    // This will upload your file to the default folder of selected in config storage
    UploadService::uploadFile($request->file('some_file'));
    
    // This will upload your file to the given as second parameter path of default storage
    UploadService::uploadFile($request->file('some_file'), 'path/to/upload');
    
    // This will upload your file to the given storage
    UploadService::uploadFile($request->file('some_file'), 'path/to/upload', 'storage_name');
    
    // This will also resize image to the given width and height
    UploadService::uploadFile($request->file('some_file'), 'path/to/upload', 'storage_name');
}
```


برای آپلود عکس با فرمت base64:

```php
public function store(Request $request)
{   
    // This will upload your file to the default folder of selected in config storage
    UploadService::uploadBase64Image($request->input('image'));
    
    // This will upload your file to the given as second parameter path of default storage
    UploadService::uploadFile($request->input('image'), 'path/to/upload');
    
    // This will upload your file to the given storage
    UploadService::uploadFile($request->input('image'), 'path/to/upload', 'storage_name');
    
    // This will also resize image to the given width and height
    UploadService::uploadFile($request->input('image'), 'path/to/upload', 'storage_name');
}
```

### امکانات 
❤️
- [x] Write the press release
- [ ] Update the website
- [ ] Contact the media 


### ویژگی ها 

* مناسب برای استفاده ای پی ای ها
* شخصی سازی بالا
  
    
