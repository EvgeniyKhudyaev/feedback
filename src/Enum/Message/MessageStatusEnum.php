<?php

namespace App\Enum\Message;

enum MessageStatusEnum: string
{
    case SUCCESS = 'success';
    case ERROR = 'error';

    public static function getChoices(): array
    {
        return [
            'Успех' => self::SUCCESS,
            'Ошибка' => self::ERROR,
        ];
    }
}
