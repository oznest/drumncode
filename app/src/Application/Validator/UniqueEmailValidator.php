<?php

declare(strict_types=1);

namespace App\Application\Validator;

use App\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueEmailValidator extends ConstraintValidator
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueEmail) {
            throw new \LogicException('Неверный тип валидатора');
        }

        if (null === $value || '' === $value) {
            return;
        }

        $existing = $this->em->getRepository(User::class)
            ->findOneBy(['email' => $value]);

        if ($existing !== null) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
