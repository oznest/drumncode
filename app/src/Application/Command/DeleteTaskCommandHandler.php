<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Infrastructure\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DeleteTaskCommandHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
        private AuthorizationCheckerInterface $auth
    ) {
    }

    public function __invoke(DeleteTaskCommand $command): void
    {
        $task = $this->taskRepository->find($command->taskId);
        if (!$task) {
            throw new \RuntimeException('Task not found');
        }

        if (!$this->auth->isGranted('DELETE', $task)) {
            throw new AccessDeniedException();
        }

        $this->taskRepository->delete($task);
    }
}
