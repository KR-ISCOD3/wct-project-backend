<?php

namespace App\Enums;

enum UserRole: string
{
    case TEACHER = 'teacher';
    case ASSISTANT = 'assistant';
    case ADMIN = 'admin';
}
