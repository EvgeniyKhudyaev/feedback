<?php

namespace App\Enum;

enum UserRole: string
{
    case Admin = 'admin';
    case Manager = 'manager';
    case Member = 'member';

    public static function getChoices(): array
    {
        return [
            'Администратор' => self::Admin->value,
            'Менеджер' => self::Manager->value,
            'Участник' => self::Member->value,
        ];
    }
}