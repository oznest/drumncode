<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Input\Http\Controller;

use App\Application\Command\CreateTaskCommand;
use App\Application\Command\DeleteTaskCommand;
use App\Application\Command\UpdateTaskStatusCommand;
use App\Application\DTO\Task\CreateTaskDto;
use App\Application\DTO\Task\DeleteTaskDto;
use App\Application\DTO\Task\UpdateStatusDto;
use App\Domain\Entity\Task;
use App\Domain\Entity\User;
use App\Infrastructure\Tracing\TracerFactory;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\ByteString;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TasksController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SerializerInterface $serializer,
        private readonly MessageBusInterface $messageBus,
        private readonly ValidatorInterface $validator
    ) {
    }

    #[Route('/api/tasks/{id}', name: 'api_task_show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/tasks/{id}',
        summary: 'Get task by ID',
        tags: ['Tasks'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns task data',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'priority', type: 'integer'),
                        new OA\Property(property: 'title', type: 'string'),
                        new OA\Property(property: 'status', type: 'string', enum: ['todo', 'done'])
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Task not found'
            )
        ]
    )]
    public function show(
        int $id,
        TracerFactory $tracerFactory,
        Request $request,
    ): Response {
        $task = $this->entityManager->getRepository(Task::class)->find($id);
        $json = $this->serializer->serialize($task, 'json', ['groups' => ['task:read']]);


        $tracer = $tracerFactory->getTracer();
        $span = $tracer
            ->spanBuilder('TaskController.show')
            ->setAttribute('http.method', $request->getMethod())
            ->setAttribute('http.target', $request->getPathInfo())
            ->setAttribute('http.host', $request->getHost())
            ->startSpan();
        $scope = $span->activate();
        try {
            return JsonResponse::fromJsonString($json);
        } finally {
            $scope->detach();

            $span->end();
        }
    }

    #[Route('/api/tasks', name: 'task_create', methods: ['POST'])]
    #[OA\Post(
        path: '/api/tasks',
        summary: 'Create a new task',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['priority'],
                properties: [
                    new OA\Property(property: 'priority', type: 'integer', example: 1),
                    new OA\Property(property: 'title', type: 'string', example: 'title'),
                    new OA\Property(property: 'description', type: 'string', example: 'description'),
                    new OA\Property(property: 'parent', type: 'integer', example: 1),
                ]
            )
        ),
        tags: ['Tasks'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Task Created',
            )
        ]
    )]
    public function create(
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $taskDto = $this->serializer->deserialize($request->getContent(), CreateTaskDto::class, 'json');
        $errors = $this->validator->validate($taskDto);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string)$errors], 400);
        }
        $this->messageBus->dispatch(new CreateTaskCommand($taskDto, $this->getUser()));

        $user = new User();
        $user
            ->setEmail(ByteString::fromRandom(12)->toString())
            ->setPassword('test')
            ->setRoles(['ROLE_USER']);
        ;
        $em->persist($user);
        $em->flush();

        return $this->json(['message' => 'Task created'], 201);
    }

    #[Route('/api/tasks/{id}', name: 'api_task_delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/tasks/{id}',
        summary: 'Delete a task by ID',
        tags: ['Tasks'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Task successfully deleted'
            ),
            new OA\Response(
                response: 404,
                description: 'Task not found'
            )
        ]
    )]
    public function delete(int $id): Response
    {
        $deleteDto = new DeleteTaskDto();
        $deleteDto->id = $id;

        $errors = $this->validator->validate($deleteDto);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string)$errors], 400);
        }
        $this->messageBus->dispatch(new DeleteTaskCommand($deleteDto->id));

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/tasks/{id}/status', name: 'update_task_status', methods: ['PATCH'])]
    #[OA\Patch(
        path: '/api/tasks/{id}/status',
        summary: 'Update a task status',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateStatusDto')
        ),
        tags: ['Tasks'],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID of the task to update",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Status updated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Status updated')
                    ]
                )
            )
        ]
    )]
    public function updateStatus(
        int $id,
        Request $request
    ): JsonResponse {

        $dto = $this->serializer->deserialize($request->getContent(), UpdateStatusDto::class, 'json');
        $dto->id = $id;

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }

        /** @var UpdateTaskStatusCommand $command */
        $this->messageBus->dispatch(new UpdateTaskStatusCommand($dto->id, $dto->status));

        return new JsonResponse(['message' => 'Status updated'], Response::HTTP_OK);
    }
}
