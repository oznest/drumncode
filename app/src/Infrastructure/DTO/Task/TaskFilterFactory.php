<?php
declare(strict_types=1);

namespace App\Infrastructure\DTO\Task;

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
            sort: $request->query->all('sort') ?? [],
            limit:  (int) $request->query->get('limit'),
            offset: (int) $request->query->get('offset'),
        );
    }


    private static function parseDate(?string $date): ?\DateTimeImmutable
    {
        return $date ? \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $date) ?: null : null;
    }
}