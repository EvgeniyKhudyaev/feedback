<?php

namespace App\Enum;

enum UserRoleEnum: string
{
    case ADMIN = 'ROLE_ADMIN';
    case MANAGER = 'ROLE_MANAGER';
    case MEMBER = 'ROLE_MEMBER';
    case API = 'ROLE_API';

    public static function getChoices(): array
    {
        return [
            'Администратор' => self::ADMIN,
            'Менеджер' => self::MANAGER,
            'Участник' => self::MEMBER,
        ];
    }
}