<?php

namespace App\Enums;

enum BusinessAccountStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
}