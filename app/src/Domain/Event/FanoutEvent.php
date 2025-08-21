<?php

declare(strict_types=1);

namespace App\Domain\Event;

class FanoutEvent
{
    public function __construct(
        public readonly string $eventName
    ) {
    }
}
