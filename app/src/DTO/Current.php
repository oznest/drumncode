<?php
declare(strict_types=1);

namespace App\DTO;

use JMS\Serializer\Annotation as Serializer;

class Current
{
    #[Serializer\SerializedName('temp_c')]
    private float $temperature;

    private Condition $condition;

    private int $humidity;

    #[Serializer\SerializedName('wind_kph')]
    private float $windKph;

    #[Serializer\SerializedName('last_updated')]
    private string $lastUpdated;

    public function getTemperature(): float
    {
        return $this->temperature;
    }

    public function getCondition(): Condition
    {
        return $this->condition;
    }

    public function getHumidity(): int
    {
        return $this->humidity;
    }

    public function getWindKph(): float
    {
        return $this->windKph;
    }

    public function getLastUpdated(): string
    {
        return $this->lastUpdated;
    }
}
