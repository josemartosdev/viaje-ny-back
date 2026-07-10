<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\ApiResponder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class HealthController extends AbstractApiController
{
    #[Route('/', methods: ['GET'])]
    public function root(): JsonResponse
    {
        return ApiResponder::success(['status' => 'ok', 'api' => '/api/trips']);
    }

    #[Route('/api', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return ApiResponder::success([
            'status' => 'ok',
            'endpoints' => [
                '/api/trips',
                '/api/days',
                '/api/places',
                '/api/activities',
                '/api/tickets',
            ],
        ]);
    }
}
