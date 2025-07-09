<?php
declare(strict_types=1);

namespace App\Service;

use Prometheus\CollectorRegistry;
use Prometheus\Storage\APC;

class PrometheusService
{
    private CollectorRegistry $registry;

    public function __construct()
    {
        // В пам'яті (для тесту, для продакшена краще Redis або APCu)
        $this->registry = new CollectorRegistry(new APC());
    }

    public function getRegistry(): CollectorRegistry
    {
        return $this->registry;
    }
}