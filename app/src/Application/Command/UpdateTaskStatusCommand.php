<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\Enum\TaskStatus;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

#[OA\Schema(
    schema: 'UpdateTaskStatusCommand',
    required: ['status'],
    properties: [
        new OA\Property(property: 'status', type: 'string', enum: ['todo', 'done'], example: 'todo')
    ],
    type: 'object'
)]
class UpdateTaskStatusCommand
{
    #[Groups(['update_status'])]
    public int $id;

    #[Groups(['update_status'])]
    public TaskStatus $status;
}
