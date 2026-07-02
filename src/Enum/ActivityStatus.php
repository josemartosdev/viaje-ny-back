<?php

declare(strict_types=1);

namespace App\Enum;

enum ActivityStatus: string
{
    case Planned = 'planned';
    case Reserved = 'reserved';
    case Flexible = 'flexible';
    case Done = 'done';
    case Cancelled = 'cancelled';
}
