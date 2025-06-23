<?php

declare(strict_types=1);

namespace App\Application\Query;

class SearchTasksQuery
{
    public function __construct(public string $text)
    {
    }
}
