<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterUserCommandHandler
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(RegisterUserCommand $command): void
    {

        $user = new User();
        $user
            ->setEmail($command->email)
            ->setPassword($command->password)
            ->setRoles(['ROLE_USER']);
        ;
        $hashedPassword = $this->passwordHasher->hashPassword($user, $command->password);
        $user->setPassword($hashedPassword);
        $this->entityManager->persist($user);
    }
}
