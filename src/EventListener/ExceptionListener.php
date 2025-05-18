<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Psr\Log\LoggerInterface;

// Не доделал, не подключил
class ExceptionListener
{
    public function __construct(private readonly LoggerInterface $logger) {}

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $this->logger->error('Ошибка приложения: ' . $exception->getMessage());

        $response = new Response();
        $response->setContent('Извините, произошла ошибка. Попробуйте позже.');
        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);

        $event->setResponse($response);
    }
}
