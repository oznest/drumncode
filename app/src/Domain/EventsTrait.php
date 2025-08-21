<?php

declare(strict_types=1);

namespace App\Domain;

trait EventsTrait
{
    private array $recordedEvents = [];

    public function record(object $event): void
    {
        $this->recordedEvents[] = $event;
    }

    public function releaseEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];

        return $events;
    }
}
