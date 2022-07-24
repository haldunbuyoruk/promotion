<?php


namespace App\EventListener;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $response = new JsonResponse([
            'type' => 'ConstrainViolationList',
            'title' => 'An error occurred',
            'description' => 'This value should be positive',
            'violations' => [
                [
                    'propertyPath' => 'quantity',
                    'message' => 'This value should be positive'
                ]
            ]
        ]);

        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);;

        if($exception instanceof  HttpExceptionInterface){
            $response->setStatusCode($exception->getStatusCode());
        }

        $event->setResponse($response);
    }
}
