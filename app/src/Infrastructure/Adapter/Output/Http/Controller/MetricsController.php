<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Output\Http\Controller;

use App\Service\PrometheusService;
use Prometheus\RenderTextFormat;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MetricsController extends AbstractController
{
    #[Route('/metrics', name: 'metrics', methods: ['GET'])]
    public function metrics(PrometheusService $prometheusService): Response
    {
        $registry = $prometheusService->getRegistry();

        $renderer = new RenderTextFormat();
        $metrics = $registry->getMetricFamilySamples();
        $result = $renderer->render($metrics);

        return new Response($result, 200, [
            'Content-Type' => RenderTextFormat::MIME_TYPE,
        ]);
    }
}
