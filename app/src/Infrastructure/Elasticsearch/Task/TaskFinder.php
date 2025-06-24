<?php

declare(strict_types=1);

namespace App\Infrastructure\Elasticsearch\Task;

use App\Application\Query\SearchTasksQuery;
use App\Infrastructure\DTO\Task\TaskFilter;
use Elastica\Query;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Symfony\Bundle\SecurityBundle\Security;

class TaskFinder
{
    public function __construct(
        private PaginatedFinderInterface $finder, // инжектится fos_elastica.finder.task_index,
        private Security $security
    ) {
    }

    public function searchByFilter(TaskFilter $filter): array
    {
        $boolQuery = new Query\BoolQuery();
        if ($filter->hasQueryFilter()) {
            $multiMatch = new Query\MultiMatch();
            $multiMatch
                ->setQuery($filter->q)
                ->setFields(['title^2', 'description'])
                ->setType('phrase');

            $boolQuery->addMust($multiMatch);
        }

        if ($filter->hasStatusFilter()) {
            $statusFilter = new Query\Term();
            $statusFilter->setTerm('status', $filter->status);
            $boolQuery->addFilter($statusFilter);
        }

        if ($filter->hasPriorityFilter()) {
            $priorityFilter = new Query\Term();
            $priorityFilter->setTerm('priority', $filter->priority);
            $boolQuery->addFilter($priorityFilter);
        }

        $user = $this->security->getUser();
        $userFilter = new \Elastica\Query\Term();
        $userFilter->setTerm('user.id', $user->getId());
        $boolQuery->addFilter($userFilter);
        $searchQuery = new Query($boolQuery);

        if ($filter->hasSort()) {
            foreach ($filter->sort as $field => $direction) {
                $searchQuery->addSort([$field => ['order' => $direction]]);
            }
        }
        $searchQuery->setFrom($filter->offset);

        return $this->finder->find($searchQuery, $filter->limit);
    }
}
