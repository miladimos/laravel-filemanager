<?php


namespace Miladimos\FileManager\Services;


abstract class Service
{
    /**
     * Sanitize the folder name.
     *
     * @param $folder
     *
     * @return string
     */
    protected function cleanFolder($folder)
    {
        return DIRECTORY_SEPARATOR . trim(str_replace('..', '', $folder), DIRECTORY_SEPARATOR);
    }
}
