<?php

namespace App\Enum;

enum UserRole: string
{
    case Admin = 'ROLE_ADMIN';
    case Manager = 'ROLE_MANAGER';
    case Member = 'ROLE_MEMBER';
    case Api = 'ROLE_API';

    public static function getChoices(): array
    {
        return [
            'Администратор' => self::Admin->value,
            'Менеджер' => self::Manager->value,
            'Участник' => self::Member->value,
        ];
    }
}