<?php

declare(strict_types=1);

namespace App\Infrastructure\Tracing;

use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\API\Trace\TracerInterface;

class TracerFactory
{
    private TracerInterface $tracer;

    public function __construct(string $url,string $serviceName = 'symfony-app')
    {
        $transport = (new OtlpHttpTransportFactory())
            ->create($url, 'application/x-protobuf');
        $exporter = new SpanExporter($transport);
        $spanProcessor = new SimpleSpanProcessor($exporter);

        $tracerProvider = new TracerProvider($spanProcessor);
        $this->tracer = $tracerProvider->getTracer($serviceName);
    }

    public function getTracer(): TracerInterface
    {
        return $this->tracer;
    }
}
