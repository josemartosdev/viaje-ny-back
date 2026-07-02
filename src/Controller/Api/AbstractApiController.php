<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\ApiResponder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class AbstractApiController extends AbstractController
{
    /** @return array<string, mixed> */
    protected function parseJson(Request $request): array
    {
        $raw = trim($request->getContent());
        if ($raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($decoded)) {
            throw new \InvalidArgumentException('JSON body must be an object');
        }

        return $decoded;
    }

    protected function notFound(string $resource = 'Resource'): JsonResponse
    {
        return ApiResponder::error(sprintf('%s not found', $resource), 'NOT_FOUND', JsonResponse::HTTP_NOT_FOUND);
    }

    protected function badRequest(string $message): JsonResponse
    {
        return ApiResponder::error($message, 'BAD_REQUEST', JsonResponse::HTTP_BAD_REQUEST);
    }

    protected function validationError(ConstraintViolationListInterface $violations): JsonResponse
    {
        $fieldErrors = [];
        foreach ($violations as $violation) {
            $fieldErrors[$violation->getPropertyPath()] = (string) $violation->getMessage();
        }

        return ApiResponder::error('Validation failed', 'VALIDATION_ERROR', JsonResponse::HTTP_UNPROCESSABLE_ENTITY, $fieldErrors);
    }
}
