<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Voter;

use App\Domain\Entity\Task;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskUpdateVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === 'UPDATE' && $subject instanceof Task;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        return $user == $subject->getUser() && !$subject->isDone();
    }
}
