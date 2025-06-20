<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum TaskStatus: string
{
    case TODO = 'todo';
    case DONE = 'done';
}
