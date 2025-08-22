<?php

declare(strict_types=1);

namespace App\Infrastructure\Tracing;

use OpenTelemetry\API\Common\Time\SystemClock;
use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\SDK\Trace\SpanProcessor\BatchSpanProcessor;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\API\Trace\TracerInterface;

class TracerFactory
{
    private TracerInterface $tracer;

    public function __construct(string $serviceName = 'symfony-app')
    {
        $transport = (new OtlpHttpTransportFactory())->create('http://jaeger:4318', 'application/json');
        $exporter = new SpanExporter($transport);
        $tracerProvider = new TracerProvider(
            new BatchSpanProcessor($exporter, new SystemClock())
        );

        $this->tracer = $tracerProvider->getTracer($serviceName);
    }

    public function getTracer(): TracerInterface
    {
        return $this->tracer;
    }
}
