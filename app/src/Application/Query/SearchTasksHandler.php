<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Application\Service\TaskFinder;

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
