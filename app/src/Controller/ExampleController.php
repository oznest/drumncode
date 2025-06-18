<?php
declare(strict_types=1);

namespace App\Controller;


use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ExampleController extends AbstractController
{
    #[Route('/api/example', name: 'api_example', methods: ['GET'])]
    #[OA\Get(
        path: "/api/example",
        summary: "Получение примера",
        description: "Возвращает тестовый JSON",
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешный ответ",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Hello, API!")
                    ]
                )
            )
        ]
    )]
    public function example(): JsonResponse
    {
        return $this->json(['message' => 'Hello, API!']);
    }
}