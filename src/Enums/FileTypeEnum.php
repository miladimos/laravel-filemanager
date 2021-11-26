<?php

namespace Miladimos\FileManager\Enums;

use Miladimos\FileManager\Traits\GetConstantsEnum;

final class FileTypeEnum
{
    use GetConstantsEnum;

    const TYPE_IMAGE = 1;
    const TYPE_IMAGE_VECTOR = 2;
    const TYPE_PDF = 3;
    const TYPE_VIDEO = 4;
    const TYPE_AUDIO = 5;
    const TYPE_ARCHIVE = 6;
    const TYPE_DOCUMENT = 7;
    const TYPE_SPREADSHEET = 8;
    const TYPE_PRESENTATION = 9;
    const TYPE_PLAIN = 10;
    const TYPE_OTHER = 11;
}
