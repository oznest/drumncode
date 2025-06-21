<?php

declare(strict_types=1);

namespace App\Infrastructure\Elasticsearch\Task;

use App\Domain\Enum\TaskStatus;
use Elastica\Query;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;

class TaskFinder
{
    public function __construct(
        private PaginatedFinderInterface $finder // инжектится fos_elastica.finder.task_index
    ) {
    }

    public function searchByText(string $query, int $limit = 10): array
    {
        $boolQuery = new Query\BoolQuery();

        $multiMatch = new Query\MultiMatch();
        $multiMatch->setQuery($query);
        $multiMatch->setFields(['title^2', 'description']);

        $boolQuery->addMust($multiMatch);

        $statusFilter = new Query\Term();
        $statusFilter->setTerm('status', TaskStatus::TODO);
        $boolQuery->addFilter($statusFilter);

        $searchQuery = new Query($boolQuery);

        return $this->finder->find($searchQuery, $limit);
    }
}
