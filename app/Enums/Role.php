<?php

namespace App\Enums;

enum Role: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Member = 'member';
    case Sales = 'sales';
    case Manager = 'manager';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin',
            self::Member => 'Member',
            self::Sales => 'Sales',
            self::Manager => 'Manager',
        };
    }
}
