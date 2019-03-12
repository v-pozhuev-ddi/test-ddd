<?php

namespace App\Ddd\Application\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Validator\Exception\ValidatorException;

class ApiExceptionListener
{

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        $errorBody = [
            'code'    => $exception->getCode(),
            'message' => $exception->getMessage(),
        ];

        if ($exception instanceof ValidatorException) {
            $errorBody['message'] = 'Has been sent incorrect data.';
        }

        $event->setResponse(new JsonResponse(['success' => false, 'error' => $errorBody]));
    }
}
