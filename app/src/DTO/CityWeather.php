<?php
declare(strict_types=1);

namespace App\DTO;

class CityWeather
{
    private Current $current;

    public function getCurrent(): Current
    {
        return $this->current;
    }
}
