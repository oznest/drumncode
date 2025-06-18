<?php
declare(strict_types=1);

namespace App\DTO;

class Location
{
    private string $name;

    private string $country;

    public function getName(): string
    {
        return $this->name;
    }

    public function getCountry(): string
    {
        return $this->country;
    }
}
