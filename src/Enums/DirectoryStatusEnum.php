<?php

namespace Miladimos\FileManager\Enums;

use Miladimos\FileManager\Traits\GetConstantsEnum;

final class DirectoryStatusEnum
{
    use GetConstantsEnum;

    const ACTIVE = 'a';
    const HIDE = 'h';
    const LOCKED = 'l';
    const PRIVATE = 'p';
}
