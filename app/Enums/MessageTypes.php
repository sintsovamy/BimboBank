<?php

namespace App\Enums;

enum MessageTypes: string
{
    case USER = 'user';
    case SUPPORT = 'support';
    case TECHNICAL = 'technical';
}
