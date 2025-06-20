<?php

namespace App\Infrastructure\Adapter\Input\Http\Controller;

use App\Application\Command\CreateTaskCommand;
use App\Application\Command\DeleteTaskCommand;
use App\Application\Command\UpdateTaskStatusCommand;
use App\Domain\Entity\Task;
use App\Infrastructure\DTO\TaskCreateDto;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class TasksController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private MessageBusInterface $messageBus
    ) {
    }

    #[Route('/api/tasks', name: 'app_tasks', methods: ['GET'])]
    public function index(): Response
    {
        $all = $this->entityManager->getRepository(Task::class)->findAll();
        $json = $this->serializer->serialize($all, 'json', ['groups' => ['task:read']]);
        return JsonResponse::fromJsonString($json);
    }

    #[Route('/api/tasks/{id}', name: 'api_task_show', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns task data',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'id', type: 'integer'),
                new OA\Property(property: 'priority', type: 'integer'),
                new OA\Property(property: 'title', type: 'integer'),
                new OA\Property(property: 'status', type: 'string', enum: ['todo', 'done'],),
            ]
        )
    )]
    public function show(int $id): Response
    {
        $task = $this->entityManager->getRepository(Task::class)->find($id);
        $json = $this->serializer->serialize($task, 'json', ['groups' => ['task:read']]);
        return JsonResponse::fromJsonString($json);
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
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse {
        $taskDto = $serializer->deserialize($request->getContent(), TaskCreateDto::class, 'json');
        $errors = $validator->validate($taskDto);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }
        $this->messageBus->dispatch(new CreateTaskCommand($taskDto, $this->getUser()));
        return $this->json(['message' => 'User created'], 201);
    }

    #[Route('/api/tasks/{id}', name: 'api_task_delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/tasks/{id}',
        summary: 'Delete a task by ID',
        tags: ['Task'],
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
        try {
            $this->messageBus->dispatch(new DeleteTaskCommand($id));
        } catch (\RuntimeException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/tasks/{id}/status', name: 'update_task_status', methods: ['PATCH'])]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/UpdateTaskStatusCommand')
    )]
    #[OA\Response(response: 200, description: 'Status updated')]
    public function updateStatus(
        int $id,
        Request $request,
        SerializerInterface $serializer,
        MessageBusInterface $bus
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $data['id'] = $id;

        /** @var UpdateTaskStatusCommand $command */
        $command = $serializer->deserialize(
            json_encode($data),
            UpdateTaskStatusCommand::class,
            'json',
            ['groups' => ['update_status']]
        );

        $bus->dispatch($command);

        return new JsonResponse(['message' => 'Status updated']);
    }
}
