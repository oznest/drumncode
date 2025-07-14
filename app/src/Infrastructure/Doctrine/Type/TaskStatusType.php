<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Type;

use App\Domain\Enum\TaskStatus;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class TaskStatusType extends Type
{
    public const NAME = 'task_status';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $platform->getVarcharTypeDeclarationSQL([
            'length' => 20,
        ]);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?TaskStatus
    {
        return $value !== null ? TaskStatus::from($value) : null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }
        if (!$value instanceof TaskStatus) {
            throw new \InvalidArgumentException('Invalid TaskStatus enum value.');
        }

        return $value->value;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
