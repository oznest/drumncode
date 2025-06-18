<?php
declare(strict_types=1);

namespace App\Domain\Entity;

enum TaskStatus:string
{
    case TODO = 'todo';
    case DONE = 'done';
}
