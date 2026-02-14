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
            self::SUPERADMIN => __('ui.role_superadmin'),
            self::ADMIN => __('ui.role_admin'),
            self::OPERATOR => __('ui.role_operator'),
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
