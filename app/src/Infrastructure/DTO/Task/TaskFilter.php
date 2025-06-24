<?php
declare(strict_types=1);

namespace App\Infrastructure\DTO\Task;

use App\Domain\Enum\TaskStatus;

class TaskFilter
{
    public function __construct(
        public readonly ?TaskStatus $status,
        public readonly ?int $priority,
        public readonly ?string $q,
        public readonly array $sort = [],
        public readonly ?int $limit,
        public readonly ?int $offset,
    ) {

    }

    public function hasStatusFilter(): bool
    {
        return $this->status !== null;
    }

    public function hasQueryFilter(): bool
    {
        return $this->q !== null;
    }

    public function hasPriorityFilter(): bool
    {
        return $this->priority !== null && $this->priority > 0;
    }

    public function hasSort(): bool
    {
        return count($this->sort) > 0;
    }
}