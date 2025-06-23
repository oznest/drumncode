<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Infrastructure\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UpdateTaskStatusCommandHandler
{
    public function __construct(
        private TaskRepository $repository,
        private AuthorizationCheckerInterface $auth
    ) {
    }

    public function __invoke(UpdateTaskStatusCommand $command): void
    {
        $task = $this->repository->find($command->id);
        if (!$task) {
            throw new \RuntimeException('Task not found');
        }

        if (!$this->auth->isGranted('UPDATE', $task)) {
            throw new AccessDeniedException();
        }

        $task->setStatus($command->status);
    }
}
