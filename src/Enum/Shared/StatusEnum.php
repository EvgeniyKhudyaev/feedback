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

    public static function getChoicesView(): array
    {
        return [
            'Активный' => self::ACTIVE->value,
            'Неактивный' => self::INACTIVE->value,
            'Удалённый' => self::DELETED->value,
            'Заблокированный' => self::BLOCKED->value,
            'Архивный' => self::ARCHIVED->value,
        ];
    }
}