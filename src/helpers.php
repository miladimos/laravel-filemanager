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

