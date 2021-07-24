<?php


namespace Miladimos\FileManager\Traits;


trait ErrorHandler
{
    public $errors = [];

    public function error($error, $type)
    {
        array_push($this->errors, [$error => $type]);
    }

    public function getErrors()
    {
        return $this->errors;
    }

}
