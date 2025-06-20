<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\Entity\Task;
use App\Domain\Enum\TaskStatus;
use Doctrine\ORM\EntityManagerInterface;

class CreateTaskCommandHandler
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(CreateTaskCommand $command): void
    {
        $task = new Task($command->user);
        $task
            ->setPriority($command->dto->priority)
            ->setTitle($command->dto->title)
            ->setStatus(TaskStatus::TODO);
        $this->entityManager->persist($task);
    }
}
