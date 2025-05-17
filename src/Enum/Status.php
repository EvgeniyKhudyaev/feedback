<?php

namespace App\Enum;

enum Status: string
{
    case Active = 'active';
    case Deleted = 'deleted';
    case Blocked = 'blocked';
    case Archived = 'archived';
}