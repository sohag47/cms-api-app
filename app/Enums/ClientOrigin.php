<?php

namespace App\Enums;


enum ClientOrigin: string
{
    case LOCAL = 'local';
    case FOREIGN = 'foreign';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
