<?php

declare(strict_types=1);

namespace App\Infrastructure\DTO\Task;

use App\Domain\Enum\TaskStatus;
use Symfony\Component\Serializer\Annotation\Groups;

class UpdateStatusDto extends DeleteTaskDto
{
    #[Groups(['update_status'])]
    public TaskStatus $status;
}
