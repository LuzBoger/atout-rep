<?php

declare(strict_types=1);

namespace App\Enum;

enum StatusRequest: string
{
    case PENDING = 'pending';
    case VALIDATED = 'validated';
    case REJECTED = 'rejected';
    case COMPLETED  = 'completed';
    case CANCELLED = 'cancelled';


}
