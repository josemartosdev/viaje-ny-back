<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Api\ApiResponder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();
        if (!str_starts_with($request->getPathInfo(), '/api/')) {
            return;
        }

        $exception = $event->getThrowable();

        if ($exception instanceof HttpExceptionInterface) {
            $event->setResponse(
                ApiResponder::error(
                    $exception->getMessage() !== '' ? $exception->getMessage() : 'HTTP error',
                    'HTTP_ERROR',
                    $exception->getStatusCode()
                )
            );

            return;
        }

        if ($exception instanceof \JsonException || $exception instanceof \InvalidArgumentException) {
            $event->setResponse(ApiResponder::error($exception->getMessage(), 'BAD_REQUEST', JsonResponse::HTTP_BAD_REQUEST));

            return;
        }

        $event->setResponse(ApiResponder::error('Internal server error', 'INTERNAL_ERROR', JsonResponse::HTTP_INTERNAL_SERVER_ERROR));
    }
}
