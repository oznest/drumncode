<?php
declare(strict_types=1);

namespace App\Tests\Services;

use App\DTO\CityWeather;
use App\Services\Weather;
use GuzzleHttp\ClientInterface;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class WeatherTest extends TestCase
{
    public function testWeather(): void
    {

        $guzzleMock =  $this->createMock(ClientInterface::class);

        $guzzleMock
            ->expects(self::once())
            ->method('request')
            ->with('GET', '/v1/current.json', ['query' => ['q' => 'London', 'key' => 'key']])
        ;

        $serializerMock = $this->createMock(SerializerInterface::class);
        $serializerMock
            ->expects(self::once())
            ->method('deserialize')
            ->willReturn($this->createMock(CityWeather::class));

        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock
            ->expects(self::once())
            ->method('debug')
        ;

        $weatherService = new Weather(
            $guzzleMock,
            'key',
            $serializerMock,
            $loggerMock
        );

        $weatherService->getWeatherByCity('London');
    }
}