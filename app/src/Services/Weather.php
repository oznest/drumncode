<?php
declare(strict_types=1);

namespace App\Services;

use App\DTO\CityWeather;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;

class Weather
{
    public function __construct(
        private ClientInterface $weatherClient,
        private string $apiKey,
        private SerializerInterface $serializer,
        private LoggerInterface $weatherLogger
    ) {
    }

    /**
     * Get weather by city name
     *
     * @throws GuzzleException
     */
    public function getWeatherByCity(string $city): CityWeather
    {
        try {
            $response =  $this->weatherClient->request('GET', '/v1/current.json', ['query' => ['q' => $city, 'key' => $this->apiKey]]);
            /* @var $weather CityWeather */
            $weather = $this->serializer->deserialize($response->getBody()->getContents(), CityWeather::class, 'json');
            $logString = sprintf('%s - Погода в %s: %s°C, %s', date('Y-m-d H:i:s'), $city, $weather->getCurrent()->getTemperature(), $weather->getCurrent()->getCondition()->getText());
            $this->weatherLogger->debug($logString);
        } catch (GuzzleException $e) {
            $this->weatherLogger->error($e->getMessage());
            throw $e;
        }

        return $weather;
    }
}
