<?php

namespace App\Enum\Feedback;

enum FeedbackTargetTypeEnum: string
{
    case SERVICE = 'service';
    case SERVICE_TYPE = 'service_type';
}