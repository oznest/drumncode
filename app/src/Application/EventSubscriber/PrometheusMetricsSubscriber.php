<?php
declare(strict_types=1);

namespace App\Application\EventSubscriber;

use App\Service\PrometheusService;
use Prometheus\CollectorRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Psr\Log\LoggerInterface;

class PrometheusMetricsSubscriber implements EventSubscriberInterface
{
    private CollectorRegistry $registry;

    // Зберігатимемо час початку запиту для обчислення тривалості
    private array $startTimes = [];

    private LoggerInterface $logger;

    public function __construct(PrometheusService $prometheusService, LoggerInterface $logger)
    {
        $this->registry = $prometheusService->getRegistry();

        // Реєструємо метрики:
        $this->registry->getOrRegisterCounter(
            'api', 'request_count', 'Total number of HTTP requests',
            ['method', 'endpoint', 'status']
        );

        $this->registry->getOrRegisterHistogram(
            'api', 'request_duration_seconds', 'HTTP request duration in seconds',
            ['method', 'endpoint', 'status']
        );
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 100],   // Високий пріоритет для початку заміру часу
            KernelEvents::RESPONSE => ['onKernelResponse', -100] // Низький пріоритет для завершення
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $this->logger->error('Kernel Request');
        // Запам'ятовуємо стартовий час для конкретного запиту (key - унікальний id)
        $request = $event->getRequest();
        $endpoint = $request->getPathInfo();
        if (str_starts_with($endpoint, '/_wdt') || $endpoint === '/metrics' || str_starts_with($endpoint, '/api/doc')) {
            return;
        }

        // Пропускаємо метрики для шляху /metrics, щоб уникнути рекурсії
        if ($request->getPathInfo() === '/metrics') {
            return;
        }

        // Запам'ятовуємо стартовий час у атрибуті запиту
        $request->attributes->set('start_time', microtime(true));
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $this->logger->error('Kernel Response');
        $request = $event->getRequest();
        $response = $event->getResponse();

        if ($request->getPathInfo() === '/metrics') {
            return; // Не збираємо метрики для /metrics, інакше рекурсія
        }

        $start = $request->attributes->get('start_time');

        if ($start === null) {
            return; // Якщо не було зафіксовано старт, нічого не робимо
        }

        $duration = microtime(true) - $start;

        $method = $request->getMethod();
        $endpoint = $request->getPathInfo();
        $status = $response->getStatusCode();

        // Отримуємо метрики
        $counter = $this->registry->getCounter('api', 'request_count');
        $histogram = $this->registry->getHistogram('api', 'request_duration_seconds');

        $counter->inc([$method, $endpoint, (string)$status]);
        $histogram->observe($duration, [$method, $endpoint, (string)$status]);
    }
}