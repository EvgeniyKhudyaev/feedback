<?php

namespace App\Enum\Feedback;

enum FeedbackScope: string
{
    case GLOBAL = 'global';
    case LINKED = 'linked';
}