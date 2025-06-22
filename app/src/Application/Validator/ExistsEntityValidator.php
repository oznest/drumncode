<?php

declare(strict_types=1);

namespace App\Application\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ExistsEntityValidator extends ConstraintValidator
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof ExistsEntity || null === $value) {
            return;
        }

        $repository = $this->em->getRepository($constraint->entityClass);

        $entity = $repository->find($value);

        if (null === $entity) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ class }}', $constraint->entityClass)
                ->setParameter('{{ id }}', (string) $value)
                ->addViolation();
        }
    }
}
