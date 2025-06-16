<?php

namespace App\Enum\Feedback;

enum FeedbackTypeEnum: string
{
//    case REVIEW = 'review';
    case SURVEY = 'survey';

    public static function getChoices(): array
    {
        return [
//            self::REVIEW->value => 'Отзыв',
            self::SURVEY->value => 'Опрос',
        ];
    }
}