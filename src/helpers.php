<?php

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
    function checkPath($disk = 'local', $path)
    {
        if ($disk && $path && \Illuminate\Support\Facades\Storage::disk($disk)->exists($path))
            return true;
        return false;
    }
}
