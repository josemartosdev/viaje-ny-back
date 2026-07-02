<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\ApiResponder;
use App\Api\EntityTransformer;
use App\Entity\Day;
use App\Repository\DayRepository;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/days')]
final class DayController extends AbstractApiController
{
    public function __construct(
        private readonly DayRepository $dayRepository,
        private readonly TripRepository $tripRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $tripId = $request->query->getInt('tripId') ?: null;
        $items = $this->dayRepository->search($request->query->get('q'), $tripId);

        return ApiResponder::success(array_map(static fn(Day $day): array => EntityTransformer::day($day), $items));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $day = $this->dayRepository->find($id);
        if ($day === null) {
            return $this->notFound('Day');
        }

        return ApiResponder::success(EntityTransformer::day($day));
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $payload = $this->parseJson($request);
        } catch (\Throwable $e) {
            return $this->badRequest($e->getMessage());
        }

        $day = new Day();
        $relationshipError = $this->hydrate($day, $payload);
        if ($relationshipError !== null) {
            return $relationshipError;
        }

        $violations = $this->validator->validate($day);
        if (count($violations) > 0) {
            return $this->validationError($violations);
        }

        $this->entityManager->persist($day);
        $this->entityManager->flush();

        return ApiResponder::success(EntityTransformer::day($day), JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $day = $this->dayRepository->find($id);
        if ($day === null) {
            return $this->notFound('Day');
        }

        try {
            $payload = $this->parseJson($request);
        } catch (\Throwable $e) {
            return $this->badRequest($e->getMessage());
        }

        $relationshipError = $this->hydrate($day, $payload);
        if ($relationshipError !== null) {
            return $relationshipError;
        }

        $violations = $this->validator->validate($day);
        if (count($violations) > 0) {
            return $this->validationError($violations);
        }

        $this->entityManager->flush();

        return ApiResponder::success(EntityTransformer::day($day));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $day = $this->dayRepository->find($id);
        if ($day === null) {
            return $this->notFound('Day');
        }

        $this->entityManager->remove($day);
        $this->entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /** @param array<string, mixed> $payload */
    private function hydrate(Day $day, array $payload): ?JsonResponse
    {
        if (array_key_exists('tripId', $payload)) {
            $trip = $this->tripRepository->find((int) $payload['tripId']);
            if ($trip === null) {
                return ApiResponder::error('Trip not found', 'RELATED_NOT_FOUND', JsonResponse::HTTP_UNPROCESSABLE_ENTITY, ['tripId' => 'Trip not found']);
            }

            $day->setTrip($trip);
        }

        if (array_key_exists('date', $payload) && $payload['date'] !== null) {
            $day->setDate(new \DateTimeImmutable((string) $payload['date']));
        }

        if (array_key_exists('title', $payload)) {
            $day->setTitle((string) $payload['title']);
        }

        if (array_key_exists('notes', $payload)) {
            $day->setNotes($payload['notes'] !== null ? (string) $payload['notes'] : null);
        }

        if (array_key_exists('weatherTip', $payload)) {
            $day->setWeatherTip($payload['weatherTip'] !== null ? (string) $payload['weatherTip'] : null);
        }

        if (array_key_exists('district', $payload)) {
            $day->setDistrict($payload['district'] !== null ? (string) $payload['district'] : null);
        }

        return null;
    }
}
