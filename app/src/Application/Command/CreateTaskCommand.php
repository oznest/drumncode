<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\Entity\User;
use App\Infrastructure\DTO\Task\CreateTaskDto;

class CreateTaskCommand
{
    public function __construct(
        public readonly CreateTaskDto $dto,
        public readonly User $user
    ) {
    }
}
