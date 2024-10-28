<?php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case USER = 'user';
    case ARTIST = 'artist';
}
