<?php

namespace Miladimos\FileManager\Enums;

use Miladimos\FileManager\Traits\GetConstantsEnum;

final class FileStatusEnum
{
    use GetConstantsEnum;

    const ACTIVE = 'a';
    const HIDE = 'h';
    const LOCKED = 'l';
    const PRIVATE = 'p';
}
