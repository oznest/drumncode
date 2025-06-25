<?php

declare(strict_types=1);

namespace App\Application\DTO\Task;

use App\Domain\Enum\TaskStatus;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UpdateStatusDto',
    required: ['status'],
    properties: [
        new OA\Property(
            property: 'status',
            type: 'string',
            enum: ['todo', 'done'],
            example: 'done'
        )
    ],
    type: 'object'
)]
class UpdateStatusDto extends DeleteTaskDto
{
    #[Groups(['update_status'])]
    public TaskStatus $status;
}
