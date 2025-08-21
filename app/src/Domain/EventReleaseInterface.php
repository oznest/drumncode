<?php

declare(strict_types=1);

namespace App\Domain;

interface EventReleaseInterface
{
    public function record(object $event): void;

    public function releaseEvents(): array;
}
