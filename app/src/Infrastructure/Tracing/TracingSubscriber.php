<?php
declare(strict_types=1);

namespace App\Infrastructure\Tracing;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\API\Trace\SpanInterface;

class TracingSubscriber implements EventSubscriberInterface
{
    private TracerInterface $tracer;
    private ?SpanInterface $currentSpan = null;

    public function __construct(TracerFactory $tracerFactory)
    {
        $this->tracer = $tracerFactory->getTracer();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 100],
            KernelEvents::RESPONSE => ['onKernelResponse', -100],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!str_starts_with($request->getPathInfo(), '/api')) {
            return;
        }

        $this->currentSpan = $this->tracer
            ->spanBuilder(sprintf('%s %s', $request->getMethod(), $request->getPathInfo()))
            ->setAttribute('http.method', $request->getMethod())
            ->setAttribute('http.url', $request->getUri())
            ->startSpan();
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if ($this->currentSpan) {
            $response = $event->getResponse();
            $this->currentSpan->setAttribute('http.status_code', $response->getStatusCode());
            $this->currentSpan->end();
            $this->currentSpan = null;
        }
    }
}