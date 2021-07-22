<?php

return [

    // All of Functionality in this Directory
    'base_directory' => 'filemanager',

    /**
     *
     * route configs that you want to use default work package
     *
     * The default group settings for the elFinder routes.
     * prefix result return  =>  yourdomain.test/API_PREFIX/API_VERSION/FILE_MANAGER_PREFIX/
     *
     */
    'routes' => [
        'prefix' => env('FILE_MANAGER_PREFIX', 'file-manager'),

        'web' => [
            'middleware' => ['web', 'auth'], //Set to empty to disable middleware filter
        ],
        'api' => [
            'api_prefix' => env('API_PREFIX', 'api'),
            'api_version' => env('API_VERSION', 'v1'),
            'middleware' => ['api'], //Set to null to disable middleware filter
        ],
    ],

//    'database' => [
//        'files' => [
//            'table' => 'files',
//            'model' => \Miladimos\FileManager\Models\File::class,
//        ],
//        'file_group_table' => [
//            'table' => 'file_groups'
//        ],
//        'directories' => [
//            'table' => 'directories',
//            'model' => \Miladimos\FileManager\Models\Directory::class,
//        ],
//    ],

    /**
     * List of disk names that you want to use for upload
     *
     * local, public, ftp
     *
     */
    'disk' => env('UPLOAD_DISK', 'storage'),

    /**
     * The maximum upload file size of an item in bytes.
     * Adding a larger file will result in an exception.
     */
    'max_file_size' => 1024 * 1024 * 10,

    'max_image_width' => 1024,

    'max_image_height' => 1024,

    'image_quality' => 80,

    /*
     * strategies
     */
    'strategies' => [
        /*
        * Thumbnail size in pixel
        */
        'thumbnail' => [
            'path' => 'thumbnails',
            'height' => 250,
            'width' => 250,
            'fit' => 'stretch', // ['stretch', 'crop', 'contain', 'max', 'fill']
            'crop' => [
                'x' => 100,
                'y' => 100
            ],
            'validation' => 'required|mimes:jpeg,png,gif',
            'mimes' => ['image/jpeg', 'image/png', 'image/bmp', 'image/gif'],
            'max_size' => '2m',
            'disk' => env('FILESYSTEM_DRIVER', 'public'),
            /*
             * Default directory template.
             * Variables:
             *  - `Y`   Year, example: 2019
             *  - `m`   Month, example: 04
             *  - `d`   Date, example: 08
             *  - `H`   Hour, example: 12
             *  - `i`   Minute, example: 03
             *  - `s`   Second, example: 12
             */
            'directory' => 'uploads/{Y}/{m}/{d}',
        ],
        'avatar' => [
            'path' => 'thumbnails',
            "date_time_prefix" => false,
            'height' => 250,
            'width' => 250,
            'fit' => 'stretch', // ['stretch', 'crop', 'contain', 'max', 'fill']
            'crop' => [
                'x' => 100,
                'y' => 100
            ],
            "sizes" => ["16", "24", "32", "64", "128", "320"],
            "thumb" => "320",
            'validation' => 'required|mimes:jpeg,png,gif',
            'mimes' => ['image/jpeg', 'image/png', 'image/bmp', 'image/gif'],
            'max_size' => '2m',
            'disk' => env('FILESYSTEM_DRIVER', 'public'),
            /*
             * Default directory template.
             * Variables:
             *  - `Y`   Year, example: 2019
             *  - `m`   Month, example: 04
             *  - `d`   Date, example: 08
             *  - `H`   Hour, example: 12
             *  - `i`   Minute, example: 03
             *  - `s`   Second, example: 12
             */
            'directory' => 'uploads/{Y}/{m}/{d}',
        ],

    ],

    /*
     * Configure the Access Mode of the uploaded files.
     * By default S3 uploads are private, we're setting them to public here.
     */
    'access' => env('MEDIA_MANAGER_ACCESS', 'public'),

    'allowed_mimes' => [
        'image/gif',
        'image/jpeg',
        'image/png',
        'image/bmp',
        'image/png',
        'image/tiff',
    ],

    'allow_format' => ['jpeg', 'jpg', 'png', 'gif', 'webp', 'docx', 'pdf', 'ttf'],

    'pagination' => [
        'folders' => 12, //2 rows

        'files' => 15, //3 rows
    ],

    /**
     * Image cache ( Intervention Image Cache )
     *
     * set 0 - if you don't need cache (default)
     * if you want use cache - set the number of minutes for which the value should be cached
     */
    'cache' => 0,

    /**
     *
     * Default Locale
     * available locale : en - fa
     * for display texts
     */
    'locale' => 'fa',

    /**
     * Show / Hide system files and folders
     */
    'hiddenFiles' => false,

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
