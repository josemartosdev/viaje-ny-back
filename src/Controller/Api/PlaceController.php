<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\ApiResponder;
use App\Api\EntityTransformer;
use App\Entity\Place;
use App\Enum\PlaceType;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/places')]
final class PlaceController extends AbstractApiController
{
    public function __construct(
        private readonly PlaceRepository $placeRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $type = $request->query->get('type');
        $parsedType = $type ? PlaceType::tryFrom($type) : null;
        $minPriceLevel = $request->query->getInt('minPriceLevel') ?: null;
        $maxPriceLevel = $request->query->getInt('maxPriceLevel') ?: null;

        $items = $this->placeRepository->findByFilters($parsedType, $minPriceLevel, $maxPriceLevel, $request->query->get('q'));

        return ApiResponder::success(array_map(static fn(Place $place): array => EntityTransformer::place($place), $items));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $place = $this->placeRepository->find($id);
        if ($place === null) {
            return $this->notFound('Place');
        }

        return ApiResponder::success(EntityTransformer::place($place));
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $payload = $this->parseJson($request);

        $place = new Place();
        $error = $this->hydrate($place, $payload);
        if ($error !== null) {
            return $error;
        }

        $violations = $this->validator->validate($place);
        if (count($violations) > 0) {
            return $this->validationError($violations);
        }

        $this->entityManager->persist($place);
        $this->entityManager->flush();

        return ApiResponder::success(EntityTransformer::place($place), JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $place = $this->placeRepository->find($id);
        if ($place === null) {
            return $this->notFound('Place');
        }

        $payload = $this->parseJson($request);

        $error = $this->hydrate($place, $payload);
        if ($error !== null) {
            return $error;
        }

        $violations = $this->validator->validate($place);
        if (count($violations) > 0) {
            return $this->validationError($violations);
        }

        $this->entityManager->flush();

        return ApiResponder::success(EntityTransformer::place($place));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $place = $this->placeRepository->find($id);
        if ($place === null) {
            return $this->notFound('Place');
        }

        $this->entityManager->remove($place);
        $this->entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /** @param array<string, mixed> $payload */
    private function hydrate(Place $place, array $payload): ?JsonResponse
    {
        if (array_key_exists('name', $payload)) {
            $place->setName((string) $payload['name']);
        }

        if (array_key_exists('type', $payload) && $payload['type'] !== null) {
            $type = PlaceType::tryFrom((string) $payload['type']);
            if ($type === null) {
                return ApiResponder::error('Invalid place type', 'BAD_REQUEST', JsonResponse::HTTP_BAD_REQUEST, ['type' => 'Invalid enum value']);
            }
            $place->setType($type);
        }

        if (array_key_exists('address', $payload)) {
            $place->setAddress($payload['address'] !== null ? (string) $payload['address'] : null);
        }

        if (array_key_exists('lat', $payload)) {
            $place->setLat($payload['lat'] !== null ? (float) $payload['lat'] : null);
        }

        if (array_key_exists('lng', $payload)) {
            $place->setLng($payload['lng'] !== null ? (float) $payload['lng'] : null);
        }

        if (array_key_exists('priceLevel', $payload)) {
            $place->setPriceLevel($payload['priceLevel'] !== null ? (int) $payload['priceLevel'] : null);
        }

        if (array_key_exists('averagePrice', $payload)) {
            $place->setAveragePrice($payload['averagePrice'] !== null ? (string) $payload['averagePrice'] : null);
        }

        if (array_key_exists('currency', $payload)) {
            $place->setCurrency($payload['currency'] !== null ? (string) $payload['currency'] : null);
        }

        if (array_key_exists('website', $payload)) {
            $place->setWebsite($payload['website'] !== null ? (string) $payload['website'] : null);
        }

        if (array_key_exists('phone', $payload)) {
            $place->setPhone($payload['phone'] !== null ? (string) $payload['phone'] : null);
        }

        if (array_key_exists('notes', $payload)) {
            $place->setNotes($payload['notes'] !== null ? (string) $payload['notes'] : null);
        }

        return null;
    }
}
