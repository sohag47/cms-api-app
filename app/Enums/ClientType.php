<?php

namespace App\Enums;

enum ClientType: string
{
    case CUSTOMER = 'customer';
    case SUPPLIER = 'supplier';
    case BOTH = 'both';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
