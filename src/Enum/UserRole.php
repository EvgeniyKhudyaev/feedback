<?php

namespace App\Enum;

enum UserRole: string
{
    case Admin = 'ADMIN';
    case Manager = 'MANAGER';
    case Member = 'MEMBER';

    public static function getChoices(): array
    {
        return [
            'Администратор' => self::Admin->value,
            'Менеджер' => self::Manager->value,
            'Участник' => self::Member->value,
        ];
    }
}