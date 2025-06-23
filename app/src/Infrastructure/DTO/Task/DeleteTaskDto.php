<?php

declare(strict_types=1);

namespace App\Infrastructure\DTO\Task;

use App\Application\Validator\ExistsEntity;
use Symfony\Component\Validator\Constraints as Assert;

class DeleteTaskDto
{
    #[Assert\NotBlank]
    #[ExistsEntity(entityClass: \App\Domain\Entity\Task::class)]
    public int $id;
}
