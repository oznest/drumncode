<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Input\Http\Controller;

use App\Application\Query\SearchTasksQuery;
use App\Domain\Entity\Task;
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
                description: 'Строка для поиска по названию и описанию',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список задач, соответствующих запросу',
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
    public function __invoke(Request $request): JsonResponse
    {
        $queryText = $request->query->get('q', '');
        /** @var Task[] $results */
        $results = $this->handle(new SearchTasksQuery($queryText));

        $json = $this->serializer->serialize($results, 'json', ['groups' => ['task:read']]);
        return JsonResponse::fromJsonString($json);
    }
}
