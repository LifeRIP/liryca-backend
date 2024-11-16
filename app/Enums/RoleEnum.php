<?php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case USER = 'user';
    case ARTIST = 'artist';

    public static function getValues(): array
    {
        return array_column(RoleEnum::cases(), 'value');
    }
}
