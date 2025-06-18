<?php
declare(strict_types=1);

namespace App\DTO;

class Condition
{
    private string $text;

    public function getText(): string
    {
        return $this->text;
    }
}
