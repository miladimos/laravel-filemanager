<?php

return [

    // All of Functionality in this Directory
    'base_directory' => env("FILEMANAGER_BASE_DIR", 'filemanager'),

    /**
     *
     * route configs that you want to use default work package
     *
     * The default group settings for the elFinder routes.
     * prefix result return  =>  yourdomain.test/API_PREFIX/API_VERSION/FILE_MANAGER_PREFIX/
     *
     */
    'routes' => [
        'prefix' => env('FILEMANAGER_ROUTE_PREFIX', 'filemanger'),
        'web' => [
            'middleware' => ['web', 'auth'], //Set to empty to disable middleware filter
        ],
        'api' => [
            'api_prefix' => env('FILEMANAGER_API_PREFIX', 'api'),
            'middleware' => ['api'], //Set to null to disable middleware filter
        ],
    ],

    'database' => [
        // for track who work with filemanger (create directory, upload, ...)
        'user_model' => \App\Models\User::class,

        'files' => [
            'table' => 'files',
            'model' => \Miladimos\FileManager\Models\File::class,
        ],
        'file_group_table' => [
            'table' => 'file_groups'
        ],
        'directories' => [
            'table' => 'directories',
            'model' => \Miladimos\FileManager\Models\Directory::class,
        ],
    ],

    /**
     * List of disk names that you want to use for upload
     *
     * local, public
     *
     */
    'disk' => env('FILEMANAGER_DISK', 'local'),

    /**
     *
     * Default Locale
     * available locale : en - fa
     * for display texts
     */
    'locale' => env("FILEMANAGER_LOCALE", 'en'),

    'download_link_expire' => '5', // in minute

    /**
     * The maximum upload file size of an item in bytes.
     * Adding a larger file will result in an exception.
     */
    'max_file_size' => 1024 * 1024 * 10,

    'max_image_width' => 1024,

    'max_image_height' => 1024,

    'image_quality' => 80,

    /**
     * strategies
     *
     * directory path template.
     * Variables:
     *  - `Y`   Year, example: 2021
     *  - `m`   Month, example: 04
     *  - `d`   Date, example: 08
     *  - `H`   Hour, example: 12
     *  - `i`   Minute, example: 15
     *
     * sizes in pixel
     */

    'strategies' => [
        'file' => [
            'path' => 'files/{Y}/{m}/{d}/timestamp-$originalFilename',
            "date_time_prefix" => true, // before name : time_name.ext
            'max_size' => '2m',
        ],
        'thumbnail' => [
            'path' => 'thumbnails/{Y}/{m}/{d}/timestamp-$originalFilename',
            "date_time_prefix" => false, // before name : time_name.ext
            'height' => 60,
            'width' => 60,
            'fit' => 'stretch', // ['stretch', 'crop', 'contain', 'max', 'fill']
            'max_size' => '2m',
        ],
        'avatar' => [
            'path' => 'thumbnails/{Y}/{m}/{d}/timestamp-$originalFilename',
            "date_time_prefix" => false, // before name : time_name.ext
            'height' => 250,
            'width' => 250,
            'fit' => 'stretch', // ['stretch', 'crop', 'contain', 'max', 'fill']
            "sizes" => ["16", "24", "32", "64", "128", "320"],
            "thumb" => "320",
            'max_size' => '2m',
        ],
    ],

    // for uploads
    'allowed_mimes' => [
        'image/gif',
        'image/jpeg',
        'image/png',
        'image/bmp',
        'image/png',
        'image/tiff',
        'application/json',
        'application/x-tar',
        'application/zip',
    ],

    // for uploads
    'allowed_extensions' => [
        'jpeg', 'jpg', 'png', 'gif', 'webp', 'docx',
        'pdf', 'ttf', 'css', 'php', 'html', 'htm', 'js',
        'xls', 'txt', 'xlsx', 'docx', 'pdf', 'rar', 'zip',
        'mp4', 'mp3', 'csv', 'cv', 'tar', 'bz2',
    ],

    // for uploads
    'disallow_extensions' => ['exe', 'asm', 'bin', 'o', 'jar'],

    'hide_files_extension' => true,

    /**
     * Show / Hide system files and folders
     */
    'hiddenFiles' => false,

    'pagination' => [
        'folders' => 12, //2 rows

        'files' => 15, //3 rows
    ],

    'logger' => [
        'active' => false,
        'driver' => ''
    ],

    /**
     * Image cache ( Intervention Image Cache )
     *
     * set 0 - if you don't need cache (default)
     * if you want use cache - set the number of minutes for which the value should be cached
     */
    'cache' => 0,

    /***************************************************************************
     * ACL rules list - used for default ACL repository (ConfigACLRepository)
     *
     * 1 it's user ID
     * null - for not authenticated user
     *
     * 'disk' => 'disk-name'
     *
     * 'path' => 'folder-name'
     * 'path' => 'folder1*' - select folder1, folder12, folder1/sub-folder, ...
     * 'path' => 'folder2/*' - select folder2/sub-folder,... but not select folder2 !!!
     * 'path' => 'folder-name/file-name.jpg'
     * 'path' => 'folder-name/*.jpg'
     *
     * * - wildcard
     *
     * access: 0 - deny, 1 - read, 2 - read/write
     */
    'aclRules' => [
        null => [
            //['disk' => 'public', 'path' => '/', 'access' => 2],
        ],
        1 => [
            //['disk' => 'public', 'path' => 'images/arch*.jpg', 'access' => 2],
            //['disk' => 'public', 'path' => 'files/*', 'access' => 1],
        ],
    ],

];
