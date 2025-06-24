<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Infrastructure\Elasticsearch\Task\TaskFinder;

class SearchTasksHandler
{
    public function __construct(private readonly TaskFinder $finder)
    {
    }

    public function __invoke(SearchTasksQuery $query): array
    {
        return $this->finder->searchByFilter($query->filter);
    }
}
