<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Input\Http\Controller;

use App\Application\Command\RegisterUserCommand;
use App\Infrastructure\DTO\UserRegisterDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    #[OA\Post(
        path: '/api/login',
        summary: 'Login and get JWT token',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'password')
                ]
            )
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'JWT Token',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'token', type: 'string', example: 'eyJ0eXAiOiJKV1Qi...')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Invalid credentials')
        ]
    )]
    public function login(): void
    {
        // This method is intercepted by LexikJWTAuthenticationBundle
        // So this will never be executed, but it's needed for Swagger
        throw new \LogicException('This method is intercepted by LexikJWTAuthenticationBundle.');
    }

    #[Route('/api/register', name: 'user_register', methods: ['POST'])]
    #[OA\Post(
        path: '/api/register',
        summary: 'Register a new user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['priority'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', example: 'test@test.ua'),
                    new OA\Property(property: 'password', type: 'string', example: 'password'),
                    new OA\Property(property: 'confirm_password', type: 'string', example: 'password'),
                ]
            )
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'User Registered'
            )
        ]
    )]
    public function register(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        MessageBusInterface $messageBus
    ): JsonResponse {
        $userRegisterDto = $serializer->deserialize($request->getContent(), UserRegisterDto::class, 'json');
        $errors = $validator->validate($userRegisterDto);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }
        $messageBus->dispatch(new RegisterUserCommand($userRegisterDto));
        return $this->json(['message' => 'User registered successfully'], Response::HTTP_CREATED);
    }
}
