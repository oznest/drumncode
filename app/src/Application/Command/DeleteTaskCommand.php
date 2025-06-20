<?php

declare(strict_types=1);

namespace App\Application\Command;

readonly class DeleteTaskCommand
{
    public function __construct(
        public int $taskId
    ) {
    }
}
