<?php

declare(strict_types=1);

namespace App\Application\EventSubscriber;

use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\Exporter\OtlpHttp\Exporter;
use OpenTelemetry\API\Trace\TracerInterface;

class TracerService
{
    private TracerInterface $tracer;

    public function __construct()
    {
        $exporter = new Exporter('http://jaeger:4318/v1/traces');

        $tracerProvider = new TracerProvider();
        $tracerProvider->addSpanProcessor(new SimpleSpanProcessor($exporter));

        $this->tracer = $tracerProvider->getTracer('symfony-app');
    }

    public function getTracer(): TracerInterface
    {
        return $this->tracer;
    }
}
