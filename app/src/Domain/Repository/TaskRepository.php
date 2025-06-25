<?php

declare(strict_types=1);

namespace App\Domain\Repository;

interface TaskRepository
{
    public function deleteById(int $id): bool;
}
