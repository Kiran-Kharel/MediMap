<?php

namespace App\Enums;

enum RoleName: string
{
    case ADMIN = 'admin';
    case PHARMACY_OWNER = 'pharmacy-owner';
    case CUSTOMER = 'customer';
}