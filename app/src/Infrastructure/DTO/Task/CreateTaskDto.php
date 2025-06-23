<?php

declare(strict_types=1);

namespace App\Infrastructure\DTO\Task;

use App\Application\Validator\ExistsEntity;
use App\Domain\Entity\Task;
use Symfony\Component\Validator\Constraints as Assert;

class CreateTaskDto
{
    #[Assert\NotBlank]
    public int $priority;

    #[Assert\NotBlank]
    public string $title;

    #[Assert\NotBlank]
    public string $description;

    #[ExistsEntity(entityClass: Task::class)]
    public int $parent;
}
