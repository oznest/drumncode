<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\Enum\TaskStatus;

class UpdateTaskStatusCommand
{
    public int $id;

    public TaskStatus $status;

    public function __construct(int $id, TaskStatus $status)
    {
        $this->id = $id;
        $this->status = $status;
    }
}
