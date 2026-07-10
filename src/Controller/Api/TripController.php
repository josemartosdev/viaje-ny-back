<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\ApiResponder;
use App\Api\EntityTransformer;
use App\Entity\Trip;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/trips')]
final class TripController extends AbstractApiController
{
    public function __construct(
        private readonly TripRepository $tripRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $items = $this->tripRepository->search($request->query->get('q'));
        $data = array_map(static fn(Trip $trip): array => EntityTransformer::trip($trip), $items);

        return ApiResponder::success($data);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $trip = $this->tripRepository->find($id);
        if ($trip === null) {
            return $this->notFound('Trip');
        }

        return ApiResponder::success(EntityTransformer::tripFull($trip));
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $payload = $this->parseJson($request);

        $trip = new Trip();
        $this->hydrate($trip, $payload);

        $violations = $this->validator->validate($trip);
        if (count($violations) > 0) {
            return $this->validationError($violations);
        }

        $this->entityManager->persist($trip);
        $this->entityManager->flush();

        return ApiResponder::success(EntityTransformer::trip($trip), JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $trip = $this->tripRepository->find($id);
        if ($trip === null) {
            return $this->notFound('Trip');
        }

        $payload = $this->parseJson($request);

        $this->hydrate($trip, $payload);
        $violations = $this->validator->validate($trip);
        if (count($violations) > 0) {
            return $this->validationError($violations);
        }

        $this->entityManager->flush();

        return ApiResponder::success(EntityTransformer::trip($trip));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $trip = $this->tripRepository->find($id);
        if ($trip === null) {
            return $this->notFound('Trip');
        }

        $this->entityManager->remove($trip);
        $this->entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /** @param array<string, mixed> $payload */
    private function hydrate(Trip $trip, array $payload): void
    {
        if (array_key_exists('name', $payload)) {
            $trip->setName((string) $payload['name']);
        }

        if (array_key_exists('city', $payload)) {
            $trip->setCity((string) $payload['city']);
        }

        if (array_key_exists('startDate', $payload) && $payload['startDate'] !== null) {
            $trip->setStartDate(new \DateTimeImmutable((string) $payload['startDate']));
        }

        if (array_key_exists('endDate', $payload) && $payload['endDate'] !== null) {
            $trip->setEndDate(new \DateTimeImmutable((string) $payload['endDate']));
        }

        if (array_key_exists('currency', $payload)) {
            $trip->setCurrency((string) $payload['currency']);
        }

        if (array_key_exists('notes', $payload)) {
            $trip->setNotes($payload['notes'] !== null ? (string) $payload['notes'] : null);
        }
    }
}
