<?php

namespace App\Enums;

enum SystemRole: string
{
    case SUPER_ADMIN = 'super-admin';
    case ADMIN = 'admin';
    case USER = 'user';
}