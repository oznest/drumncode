<?php

namespace App\Infrastructure\Adapter\Input\Http\Controller;

use App\Domain\Entity\Task;
use App\Domain\Entity\TaskStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\SerializerInterface;

final class TasksController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
    ) {
    }

    #[Route('/tasks', name: 'app_tasks')]
    public function index(): Response
    {
        $all = $this->entityManager->getRepository(Task::class)->findAll();

        $task = new Task();
        $task
            ->setPriority(1)
            ->setStatus(TaskStatus::TODO)
            ->setTitle('To do')
            ;

        $this->entityManager->persist($task);
        $this->entityManager->flush();
        return $this->render('tasks/index.html.twig', [
            'controller_name' => 'TasksController',
        ]);
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
}
