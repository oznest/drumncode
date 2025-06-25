<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\DTO\Task\CreateTaskDto;
use App\Domain\Entity\User;

readonly class CreateTaskCommand
{
    public function __construct(
        public CreateTaskDto $dto,
        public User $user
    ) {
    }
}
