<?php

namespace App\Enums;

class UserRole
{
    const ADMIN = 'admin';
    const TEACHER = 'teacher';
    const ACCOUNTANT = 'accountant';

    /**
     * Get all role values
     */
    public static function all(): array
    {
        return [
            self::ADMIN,
            self::TEACHER,
            self::ACCOUNTANT,
        ];
    }

    /**
     * Get role display names
     */
    public static function displayNames(): array
    {
        return [
            self::ADMIN => 'Admin',
            self::TEACHER => 'Teacher',
            self::ACCOUNTANT => 'Accountant',
        ];
    }

    /**
     * Get display name for a role
     */
    public static function getDisplayName(string $role): string
    {
        return self::displayNames()[$role] ?? ucfirst($role);
    }
}
