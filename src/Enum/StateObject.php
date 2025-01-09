<?php

namespace App\Enum;

enum StateObject: string
{
    case NEW = 'new';
    case GOOD = 'good';
    case FAIR = 'fair';
    case DAMAGED = 'damaged';
    case BROKEN = 'broken';
    case UNREPAIRABLE = 'unrepairable';
}
