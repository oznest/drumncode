<?php

declare(strict_types=1);

namespace App\Domain\Event;

readonly class StatusChangedEvent
{
    public function __construct(
        public int $taskId
    ) {
    }
}
