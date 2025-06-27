<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\Entity\Task;
use App\Domain\Repository\TaskRepository;
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
        /** @var Task $task */
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
