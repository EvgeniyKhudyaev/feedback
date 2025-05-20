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
}