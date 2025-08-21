<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\Entity\Task;

readonly class SubtaskAddedEvent
{
    public function __construct(
        public Task $parent,
        public Task $child
    ) {
    }
}
