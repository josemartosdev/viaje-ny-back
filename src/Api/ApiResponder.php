<?php

declare(strict_types=1);

namespace App\Api;

use Symfony\Component\HttpFoundation\JsonResponse;

final class ApiResponder
{
    /**
     * @param array<string, mixed>|list<array<string, mixed>> $data
     */
    public static function success(array $data, int $status = JsonResponse::HTTP_OK): JsonResponse
    {
        return new JsonResponse($data, $status);
    }

    /**
     * @param array<string, string> $fieldErrors
     */
    public static function error(string $message, string $code, int $status, array $fieldErrors = []): JsonResponse
    {
        return new JsonResponse(
            [
                'message' => $message,
                'code' => $code,
                'fieldErrors' => $fieldErrors,
            ],
            $status
        );
    }
}
