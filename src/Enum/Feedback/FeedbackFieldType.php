<?php

namespace App\Enum\Feedback;

enum FeedbackFieldType: string
{
    case INPUT = 'input';
    case TEXTAREA = 'textarea';
    case CHECKBOX = 'checkbox';
    case SELECT = 'select';
    case RADIO = 'radio';
    case MULTISELECT = 'multiselect';
    case RATING = 'rating';

    public static function getChoices(): array
    {
        return [
            'Текст' => self::INPUT,
            'Многострочный текст' => self::TEXTAREA,
            'Чекбокс' => self::CHECKBOX,
            'Список' => self::SELECT,
            'Радио' => self::RADIO,
            'Множественный список' => self::MULTISELECT,
            'Рейтинг' =>  self::RATING,
        ];
    }
}