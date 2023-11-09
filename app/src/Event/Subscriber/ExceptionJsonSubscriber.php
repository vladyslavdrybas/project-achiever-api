<?php

declare(strict_types=1);

namespace App\Event\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\PropertyAccess\Exception\AccessException;

class ExceptionJsonSubscriber implements EventSubscriberInterface
{
    protected string $environment;

    public function __construct(string $projectEnvironment)
    {
        $this->environment = $projectEnvironment;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // the priority must be greater than the Security HTTP
            // ExceptionListener, to make sure it's called before
            // the default exception listener
            KernelEvents::EXCEPTION => ['onKernelException', 2],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $code = JsonResponse::HTTP_BAD_REQUEST;
        if ($exception instanceof AccessDeniedException) {
            $code = JsonResponse::HTTP_UNAUTHORIZED;
        } else if ($exception instanceof NotFoundHttpException) {
            $code = JsonResponse::HTTP_NOT_FOUND;
        }

        $data = [
            'message' => $exception->getMessage(),
            'status' => $code,
            'environment' => $this->environment,
        ];

        if ($this->environment !== 'prod') {
            $data['trace'] = $exception->getTrace();
        }

        $event->setResponse(new JsonResponse($data,$code));
        // or stop propagation (prevents the next exception listeners from being called)
        //$event->stopPropagation();
    }
}
