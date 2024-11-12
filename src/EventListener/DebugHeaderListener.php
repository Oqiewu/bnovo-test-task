<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Stopwatch\Stopwatch;

class DebugHeaderListener
{
    private $stopwatch;

    public function __construct(Stopwatch $stopwatch)
    {
        $this->stopwatch = $stopwatch;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $this->stopwatch->start('request');
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        $executionTime = $this->stopwatch->stop('request')->getDuration();
        $memoryUsage = round(memory_get_usage(true) / 1024, 2); // В KB

        $response->headers->set('X-Debug-Time', $executionTime . ' ms');
        $response->headers->set('X-Debug-Memory', $memoryUsage . ' KB');
    }
}
