<?php

declare(strict_types=1);

namespace App\Application\DTO\Task;

use App\Domain\Enum\TaskStatus;
use Symfony\Component\HttpFoundation\Request;

class TaskFilterFactory
{
    public static function fromRequest(Request $request): TaskFilter
    {
        $status = null;
        if ($request->query->get('status')) {
            $status = TaskStatus::from($request->query->get('status'));
        }

        return new TaskFilter(
            status: $status,
            priority: (int)$request->query->get('priority'),
            q: $request->query->get('q'),
            limit: (int) $request->query->get('limit'),
            offset: (int) $request->query->get('offset'),
            sort: $request->query->all('sort') ?? [],
        );
    }

    private static function parseDate(?string $date): ?\DateTimeImmutable
    {
        return $date ? \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $date) ?: null : null;
    }
}
