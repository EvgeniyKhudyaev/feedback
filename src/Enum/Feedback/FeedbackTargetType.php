<?php

namespace App\Enum\Feedback;

enum FeedbackTargetType: string
{
    case SERVICE = 'service';
    case SERVICE_TYPE = 'service_type';
}