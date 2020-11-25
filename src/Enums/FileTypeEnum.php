<?php


namespace Miladimos\FileManager\Enums;


use ReflectionClass;

class FileTypeEnum
{

    const  AUDIO = 1;
    const  IMAGE = 2;
    const  DOCUMENT = 3;
    const  VIDEO = 4;

    const TYPE_IMAGE = 'image';
    const TYPE_IMAGE_VECTOR = 'vector';
    const TYPE_PDF = 'pdf';
    const TYPE_VIDEO = 'video';
    const TYPE_AUDIO = 'audio';
    const TYPE_ARCHIVE = 'archive';
    const TYPE_DOCUMENT = 'document';
    const TYPE_SPREADSHEET = 'spreadsheet';
    const TYPE_PRESENTATION = 'presentation';
    const TYPE_OTHER = 'other';
    const TYPE_ALL = 'all';


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
