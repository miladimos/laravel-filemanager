<?php


namespace Miladimos\FileManager\Enums;


use ReflectionClass;

class FileTypeEnum
{

    const  AUDIO = 1;
    const  IMAGE = 2;
    const  DOCUMENT = 3;
    const  VIDEO = 4;

    static function getConstants() {
        $oClass = new ReflectionClass(__CLASS__);
        return $oClass->getConstants();
    }


    static function getConstantsValue()
    {
        return [
            self::AUDIO,
            self::IMAGE,
            self::DOCUMENT,
            self::VIDEO
        ];
    }

}
