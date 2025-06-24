<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Infrastructure\DTO\Task\TaskFilter;

class SearchTasksQuery
{
    public function __construct(public TaskFilter $filter)
    {
    }
}
