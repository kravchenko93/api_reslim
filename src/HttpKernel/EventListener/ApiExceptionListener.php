<?php
declare(strict_types=1);

namespace App\HttpKernel\EventListener;

use App\Exception\SystemException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use App\Service\JsonSchemaService;
use App\Exception\JsonSchemaValidationException;
use App\Exception\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiExceptionListener
{
    /**
     * @var JsonSchemaService $jsonSchemaService
     */
    private $jsonSchemaService;

    /**
     * @param JsonSchemaService $jsonSchemaService
     */
    public function __construct(
        JsonSchemaService $jsonSchemaService
    ) {
        $this->jsonSchemaService = $jsonSchemaService;
    }

    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        if (0 === strpos($event->getRequest()->getRequestUri(), '/api/')) {
            $this->handleErrorResponse($event);
        }
    }

    /**
     * @param ExceptionEvent $event
     *
     * @return void
     */
    private function handleErrorResponse(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if ($exception instanceof JsonSchemaValidationException) {
            $errorDetails = $this->jsonSchemaService->buildErrorDetails($exception);
            $responseData = [
                'errorType' => 'validationError',
                'errorDetails' => $errorDetails,
            ];
        } elseif($exception instanceof ValidationException) {
            $fieldErrors = [];
            foreach ($exception->getFieldsDetails() as $fieldName => $errors) {
                $field = [
                    'id' => $fieldName,
                    'errors' => [],
                ];
                foreach ($errors as $error) {
                    $field['errors'][] = [
                        'message' => $error['clientError'],
                    ];
                }
                $fieldErrors[] = $field;
            }
            $responseData = [
                'errorType'    => 'validationError',
                'errorDetails' => [
                    'fields' => $exception->getFieldsDetails(),
                    'fieldErrors' => $fieldErrors,
                ],
            ];
        } elseif($exception instanceof SystemException) {
            $responseData = [
                'errorType'   => 'serverError',
                'message' => $exception->getMessage()
            ];
        } elseif ($exception instanceof NotFoundHttpException) {
            $responseData = [
                'errorType'   => 'notFoundError',
                'message' => $exception->getMessage()
            ];
        } else {
            $responseData = [
                'errorType'   => 'serverError',
                'message' => $exception->getMessage()
            ];
        }

        $response = new JsonResponse($responseData);
        $event->setResponse($response);
    }
}