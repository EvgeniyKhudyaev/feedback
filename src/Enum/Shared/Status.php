<?php

namespace App\Enum\Shared;

enum Status: string
{
    case ACTIVE = 'active';
    case DELETED = 'deleted';
    case BLOCKED = 'blocked';
    case ARCHIVED = 'archived';
}