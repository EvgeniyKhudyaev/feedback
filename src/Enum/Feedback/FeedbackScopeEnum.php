<?php

namespace App\Enum\Feedback;

enum FeedbackScopeEnum: string
{
    case GLOBAL = 'global';
    case LINKED = 'linked';

    public static function getChoices(): array
    {
        return [
            self::GLOBAL->value => 'Глобальный',
            self::LINKED->value => 'Связанный',
        ];
    }
}