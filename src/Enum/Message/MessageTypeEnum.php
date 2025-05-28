<?php

namespace App\Enum\Message;

enum MessageTypeEnum: string
{
    case EMAIL = 'email';
    case TELEGRAM = 'telegram';

    public static function getChoices(): array
    {
        return [
            'Электронная почта' => self::EMAIL,
            'Телеграм' => self::TELEGRAM,
        ];
    }
}
