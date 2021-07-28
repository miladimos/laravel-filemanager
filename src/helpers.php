<?php

// for more than helper function see and start miladimos/laravel-toolkit

if (!function_exists('user')) {
    function user($guard = 'web')
    {
        if (!auth()->check()) {
            return false;
        }

        return auth($guard)->user();
    }
}

// if exists return true
if (!function_exists('checkPath')) {
    function checkPath($path, $disk = 'local')
    {
        if ($disk && $path && \Illuminate\Support\Facades\Storage::disk($disk)->exists($path))
            return true;
        return false;
    }
}

if (!function_exists("getUserIP")) {
    function getUserIP()
    {
        $client = $_SERVER['HTTP_CLIENT_IP'];
        $forward = $_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote = $_SERVER['REMOTE_ADDR'];

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }

        return $ip;
    }
}

if (!function_exists('decodeBase64File')) {
    function decodeBase64File($encodedFile)
    {
        // اینجا اطلاعات اضافی رو پاک میکنم تا کد اصلی رو بگیرم
        $file = str_replace(' ', '+', $encodedFile);
        $file = substr($file, strpos($file, ';base64,') + 8);
        $decodedFile = base64_decode($file);

        // با کمک توابع پی اچ پی، مشخصات فایل رو بررسی می کنم
        $fileMimeType = finfo_buffer(finfo_open(), $decodedFile, FILEINFO_MIME_TYPE);
        $fileExt = substr($fileMimeType, strpos($fileMimeType, '/') + 1);

        return [
            'file' => $decodedFile, // فایل آماده برای ذخیره سازی در دیسک
            'mime' => $fileMimeType, // نوع فایل
            'ext' => $fileExt, // اکستنشن فایل
            'size' => (int)strlen($decodedFile) // حجم فایل با واحد بایت
        ];
    }
}

if (!function_exists('version')) {
    function version(): string
    {
        return trim(file_get_contents(base_path('.version')));
    }
}

if (!function_exists('checkInstanceOf')) {
    function checkInstanceOf($varable, string $model): string
    {

        return !!($varable instanceof $model);
    }
}


