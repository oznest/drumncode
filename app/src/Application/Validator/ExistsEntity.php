<?php

declare(strict_types=1);

namespace App\Application\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ExistsEntity extends Constraint
{
    public string $message = 'Entity "{{ class }}" with id "{{ id }}" not found.';
    public string $entityClass;

    public function __construct(string $entityClass, array $options = [], ?array $groups = null, mixed $payload = null)
    {
        $options['entityClass'] ??= $entityClass;
        parent::__construct($options, $groups, $payload);

        if (!isset($options['entityClass'])) {
            throw new \InvalidArgumentException('The "entityClass" option is required.');
        }

        $this->entityClass = $options['entityClass'];
    }

    public function getRequiredOptions(): array
    {
        return ['entityClass'];
    }

    public function validatedBy(): string
    {
        return ExistsEntityValidator::class;
    }
}
