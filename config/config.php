<?php


return [

    /**
     * route configs that you want to use default work package
     * (from config/file-manager.php)
     *
     * prefix result return  =>  yourdomain.test/API_PREFIX/API_VERSION/FILE_MANAGER_API_PREFIX/
     */
    'routes' => [
        'api_prefix' => env('API_PREFIX', 'api'),
        'api_version' => env('API_VERSION', 'v1'),
        'prefix' => env('FILE_MANAGER_API_PREFIX', 'file-manager'),
        'middleware' => ['api'], //Set to null to disable middleware filter
    ],

    /**
     * web - api
     * api : if you want use this package for Apis
     * web : if you want use this package for web with blade
     */
    'uses' => 'api',

    /**
     * List of disk names that you want to use
     * (from config/filesystems)
     */
    'disks' => [
        'public',
        'ftp'
    ],

    'mimes' => [
        'image/gif',
        'image/jpeg',
        'image/png',
    ],

    /**
     * Image cache ( Intervention Image Cache )
     *
     * set null, 0 - if you don't need cache (default)
     * if you want use cache - set the number of minutes for which the value should be cached
     */
    'cache' => null,

    /**
     * File manager modules configuration
     *
     * 1 - only one file manager window
     * 2 - one file manager window with directories tree module
     * 3 - two file manager windows
     */
    'windowsConfig' => 2,

    /**
     * File upload - Max file size in KB
     *
     * null - no restrictions
     */
    'maxUploadFileSize' => null,

    /**
     * File upload - Allow these file types
     *
     * [] - no restrictions
     */
    'allowFileTypes' => [

    ],

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

    /**
     * ACL strategy
     *
     * blacklist - Allow everything(access - 2 - r/w) that is not forbidden by the ACL rules list
     *
     * whitelist - Deny anything(access - 0 - deny), that not allowed by the ACL rules list
     */
    'aclStrategy' => 'blacklist',

    /**
     * ACL Rules cache
     *
     * null or value in minutes
     */
    'aclRulesCache' => null,


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

    'use_package_routes' => '',


    /*
   |--------------------------------------------------------------------------
   | This is the storage to upload files by default
   |--------------------------------------------------------------------------
   |
   | Now you have two options here:
   | 1. To store locally FilesSaver::STORAGE_LOCAL (this is the default option)
   | 2. To store in s3 storage FilesSaver::STORAGE_AMAZON_S3
   |
   */
    'files_upload_storage' => env('FILES_UPLOAD', \Vmorozov\FileUploads\FilesSaver::STORAGE_LOCAL),

    /*
    |--------------------------------------------------------------------------
    | This is the default level of quality for the stored images
    |--------------------------------------------------------------------------
    | Possible values are from 1 to 100
    |
    */
    'image_quality' => env('IMAGE_QUALITY', \Vmorozov\FileUploads\FilesSaver::DEFAULT_IMAGE_QUALITY),


    /*
    |--------------------------------------------------------------------------
    | This is the default level of quality for the stored images
    |--------------------------------------------------------------------------
    | Possible values are from 1 to 100
    |
    */
    'image_extension' => env('IMAGE_EXTENSION', \Vmorozov\FileUploads\FilesSaver::DEFAULT_IMAGE_EXTENSION),

    /*
    |--------------------------------------------------------------------------
    | This is the default level of quality for the stored images
    |--------------------------------------------------------------------------
    | Possible values are from 1 to 100
    |
    */
    'default_uploads_folder' => env('DEFAULT_UPLOADS_FOLDER', \Vmorozov\FileUploads\FilesSaver::DEFAULT_UPLOADS_FOLDER),

    'disk' => env('UPLOAD_DISK', 'public'),

    'files' => [
        'logo' => [
            'resize' => [
                'thumb' => [
                    'height' => 100,
                    'width' => 100,
                    'fit' => 'stretch', // ['stretch', 'crop', 'contain', 'max', 'fill']
                    'crop' => [
                        'x' => 100,
                        'y' => 100
                    ],
                    'create_on_upload' => true
                ]
            ],
            'validation' => 'required|mimes:jpeg,png,gif',
            //optional:
            //'optimize' => true, //optimize image on upload
            //'keep_original_file' => true //keep the original image Note: this might take time if the image file is uploaded to cloud
        ],
    ],


    /*
    * Base uri for $response->url
    */
    'base_uri' => null,

    'strategies' => [
        /*
         * default strategy.
         */
        'default' => [
            /*
             * The form name for file.
             */
            'name' => 'file',

            /*
             * Allowed MIME types.
             */
            'mimes' => ['image/jpeg', 'image/png', 'image/bmp', 'image/gif'],

            /*
             * The disk name to store file, the value is key of `disks` in `config/filesystems.php`
             */
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

            /*
             * File size limit
             */
            'max_size' => '2m',

            /*
             * Strategy of filename.
             *
             * Available:
             *  - `random` Use random string as filename.
             *  - `md5_file` Use md5 of file as filename.
             *  - `original` Use the origin client file name.
             */
            'filename_type' => 'md5_file',
        ],

        /*
         * You can create custom strategy to override the default strategy.
         */
        'avatar' => [
            'directory' => 'avatars/{Y}/{m}/{d}',
        ],

        //...
    ],


    /*
    |--------------------------------------------------------------------------
    | Routes group config
    |--------------------------------------------------------------------------
    |
    | The default group settings for the elFinder routes.
    |
    */
];


