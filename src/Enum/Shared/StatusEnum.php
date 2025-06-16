<?php

namespace App\Enum\Shared;

enum StatusEnum: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case DELETED = 'deleted';
    case BLOCKED = 'blocked';
    case ARCHIVED = 'archived';

    public static function getChoices(): array
    {
        return [
            self::ACTIVE->value => 'Активный',
            self::INACTIVE->value => 'Неактивный',
            self::DELETED->value => 'Удалённый',
            self::BLOCKED->value => 'Заблокированный',
            self::ARCHIVED->value => 'Архивный',
        ];
    }
}