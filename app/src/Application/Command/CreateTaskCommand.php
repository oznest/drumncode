<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\Entity\User;
use App\Infrastructure\DTO\TaskCreateDto;

class CreateTaskCommand
{
    public function __construct(
        public readonly TaskCreateDto $dto,
        public readonly User $user
    ) {
    }
}
