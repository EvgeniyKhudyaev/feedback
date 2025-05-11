<?php

namespace App\Enum;

enum Status: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Archived = 'archived';
}