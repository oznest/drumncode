<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\DTO\Task\TaskFilter;

interface TaskFinder
{
    public function searchByFilter(TaskFilter $filter): array;
}
