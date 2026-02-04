<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPERADMIN = 'superadmin';
    case ADMIN = 'admin';
    case OPERATOR = 'operator';

    public function label(): string
    {
        return match($this) {
            self::SUPERADMIN => 'Super Admin',
            self::ADMIN => 'Admin',
            self::OPERATOR => 'Operator',
        };
    }

    public function color(): string 
    {
        return match($this) {
            self::SUPERADMIN => 'red',
            self::ADMIN => 'blue',
            self::OPERATOR => 'green',
        };
    }
}
