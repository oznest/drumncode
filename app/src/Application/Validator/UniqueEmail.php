<?php

declare(strict_types=1);

namespace App\Application\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class UniqueEmail extends Constraint
{
    public string $message = 'This email already in use.';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
