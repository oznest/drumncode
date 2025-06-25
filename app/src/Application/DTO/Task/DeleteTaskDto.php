<?php

declare(strict_types=1);

namespace App\Application\DTO\Task;

use App\Application\Validator\ExistsEntity;
use App\Domain\Entity\Task;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class DeleteTaskDto
{
    #[Assert\NotBlank]
    #[ExistsEntity(entityClass: Task::class)]
    #[Groups(['update_status'])]
    public int $id;
}
