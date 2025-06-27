<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Task;
use App\Domain\Repository\TaskRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Task  find(int $id)
 */
class DoctrineTaskRepository extends ServiceEntityRepository implements TaskRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function deleteById(int $id): bool
    {
        $task = $this->find($id);
        if (!$task) {
            return false;
        }

        $this->getEntityManager()->remove($task);
        $this->getEntityManager()->flush();

        return true;
    }
}
