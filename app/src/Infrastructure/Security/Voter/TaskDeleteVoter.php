<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Voter;

use App\Domain\Entity\Task;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TaskDeleteVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === 'DELETE' && $subject instanceof Task;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /* @var $subject Task */
        return $subject->canBeEditedBy($token->getUser());
    }
}
