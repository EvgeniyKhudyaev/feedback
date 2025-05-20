<?php

namespace App\Enum\Feedback;

enum FeedbackType: string
{
    case REVIEW = 'review';
    case SURVEY = 'survey';
}