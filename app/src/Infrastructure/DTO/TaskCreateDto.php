<?php

declare(strict_types=1);

namespace App\Infrastructure\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class TaskCreateDto
{
    #[Assert\NotBlank]
    public int $priority;

    #[Assert\NotBlank]
    public string $title;

    #[Assert\NotBlank]
    public string $description;
}
