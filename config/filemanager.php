<?php


return [

    /*
                 * FQCN of the model to use for media
                *
                * Should extend `Plank\Mediable\Media`
           */
    'model' => \Miladimos\FileManager\Models\File::class,

    /*
    |--------------------------------------------------------------------------
    | route configs that you want to use default work package
    |--------------------------------------------------------------------------
    |
    | The default group settings for the elFinder routes.
      prefix result return  =>  yourdomain.test/API_PREFIX/API_VERSION/FILE_MANAGER_API_PREFIX/
    |
     */
    'routes' => [
        'web' => [
            'middleware' => ['web', 'auth'], //Set to null to disable middleware filter
        ],
        'api' => [
            'api_prefix' => env('API_PREFIX', 'api'),
            'api_version' => env('API_VERSION', 'v1'),
            'middleware' => ['api'], //Set to null to disable middleware filter
        ],
        'prefix' => env('FILE_MANAGER_API_PREFIX', 'file-manager'),
    ],

    /**
     * List of disk names that you want to use for upload
     *
     * public, ftp, storage
     *
     */
    'disk' => env('UPLOAD_DISK', 'public'),

    /**
     * web - api
     * api : if you want use this package for Apis
     * web : if you want use this package for web with blade
     */
    'uses' => 'api',

    'middleware'      => ['web', 'auth'],

    'allow_format'    => 'jpeg,jpg,png,gif,webp',

    'max_size'        => 500,

    'max_image_width' => 1024,

    'image_quality'   => 80,

    "access" => "public",

    "type" => "default",

    "types" => [
        "default" => [
            "provider"                => \AliGhale\FileManager\Types\File::class,
            "path"                    => "default_files/test/",
            "private"                 => false,
            "date_time_prefix"        => true,
            "use_file_name_to_upload" => false,
            "secret"                  => "ashkdsjka#sdkdjfsj22188455$$#$%dsDFsdf",
            "download_link_expire"    => 160, // minutes
        ],

        "image"   => [
            "provider" => \AliGhale\FileManager\Types\Image::class,
            "path"     => "images/upload/documents/",
            "sizes"    => ["16", "24", "32", "64", "128", "320"],
            "thumb"    => "320"
        ],
        "profile" => [
            "parent"           => "image",
            "path"             => "images/upload/profiles/",
            "date_time_prefix" => false,
        ],
    ],

    /*
     * How many size of your image you want.
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

    ],

    /*
     * Configure the Access Mode of the uploaded files.
     * By default S3 uploads are private, we're setting them to public here.
     */
    'access' => env('MEDIA_MANAGER_ACCESS', 'public'),

    /*
    * The maximum file size of an item in bytes.
    * Adding a larger file will result in an exception.
    */
    'max_file_size' => 1024 * 1024 * 10,

    'allowed_mimes' => [
        'image/gif',
        'image/jpeg',
        'image/png',
        'image/bmp',
        'image/png',
        'image/tiff',
    ],

    'database' => [
        'files_table' => 'files',
        'file_group_table' => 'file_groups',
        'directories' => 'directories',
    ],


    'pagination_results_folders' => 12, //2 rows
    'pagination_results_files' => 15, //3 rows

    /**
     * Image cache ( Intervention Image Cache )
     *
     * set null, 0 - if you don't need cache (default)
     * if you want use cache - set the number of minutes for which the value should be cached
     */
    'cache' => null,

    /**
     *
     * Default Locale
     * available locale : en - fa - tr - ar
     *
    */
    'locale' => 'fa',

    /**
     * File upload - Max file size in KB
     *
     * null - no restrictions
     */
    'maxUploadFileSize' => null,

    /**
     * Show / Hide system files and folders
     */
    'hiddenFiles' => true,


    /***************************************************************************
     * ACL mechanism ON/OFF
     *
     * default - false(OFF)
     */
    'acl' => false,

    /**
     * Hide files and folders from file-manager if user doesn't have access
     *
     * ACL access level = 0
     */
    'aclHideFromFM' => true,


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
