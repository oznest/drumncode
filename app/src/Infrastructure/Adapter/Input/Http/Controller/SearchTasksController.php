<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Input\Http\Controller;

use App\Application\Query\SearchTasksQuery;
use App\Domain\Entity\Task;
use App\Application\DTO\Task\TaskFilterFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\SerializerInterface;

class SearchTasksController extends AbstractController
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $messageBus,
        private readonly SerializerInterface $serializer,
    ) {
        $this->messageBus = $messageBus;
    }

    #[Route('/api/tasks/search', name: 'api_tasks_search', methods: ['GET'])]
    #[OA\Get(
        path: '/api/tasks/search',
        summary: 'Поиск задач по тексту',
        tags: ['Tasks'],
        parameters: [
            new OA\Parameter(
                name: 'q',
                description: 'Search string',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'status',
                description: 'Filter tasks by status',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['todo', 'done']
                )
            ),
            new OA\Parameter(
                name: 'priority',
                description: 'Priority from 1 to 5',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'integer',
                    maximum: 5,
                    minimum: 1
                )
            ),
            new OA\Parameter(
                name: 'sort[createdAt]',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['asc', 'desc']
                )
            ),
            new OA\Parameter(
                name: 'sort[completedAt]',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['asc', 'desc']
                )
            ),
            new OA\Parameter(
                name: 'sort[priority]',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['asc', 'desc']
                )
            ),
            new OA\Parameter(
                name: 'limit',
                description: 'Maximum number of results to return',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', minimum: 1, default: 10)
            ),
            new OA\Parameter(
                name: 'offset',
                description: 'Number of results to skip',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', minimum: 0, default: 0)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Task list',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer'),
                            new OA\Property(property: 'title', type: 'string'),
                            new OA\Property(property: 'description', type: 'string'),
                        ],
                        type: 'object'
                    )
                )
            )
        ]
    )]
    public function __invoke(
        Request $request
    ): JsonResponse {
        $filter = TaskFilterFactory::fromRequest($request);
        /** @var Task[] $results */
        $results = $this->handle(new SearchTasksQuery($filter));
        $json = $this->serializer->serialize($results, 'json', ['groups' => ['task:read']]);

        return JsonResponse::fromJsonString($json);
    }
}
