<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\ApiResponder;
use App\Api\EntityTransformer;
use App\Entity\Activity;
use App\Enum\ActivityStatus;
use App\Repository\ActivityRepository;
use App\Repository\DayRepository;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/activities')]
final class ActivityController extends AbstractApiController
{
    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly DayRepository $dayRepository,
        private readonly PlaceRepository $placeRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $dayId = $request->query->getInt('dayId') ?: null;
        $status = $request->query->get('status');
        $parsedStatus = $status ? ActivityStatus::tryFrom($status) : null;
        $category = $request->query->get('category');
        $items = $this->activityRepository->findByFilters($dayId, $parsedStatus, $category, $request->query->get('q'));

        return ApiResponder::success(array_map(static fn(Activity $activity): array => EntityTransformer::activity($activity), $items));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $activity = $this->activityRepository->find($id);
        if ($activity === null) {
            return $this->notFound('Activity');
        }

        return ApiResponder::success(EntityTransformer::activity($activity));
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $payload = $this->parseJson($request);

        $activity = new Activity();
        $error = $this->hydrate($activity, $payload);
        if ($error !== null) {
            return $error;
        }

        $violations = $this->validator->validate($activity);
        if (count($violations) > 0) {
            return $this->validationError($violations);
        }

        $this->entityManager->persist($activity);
        $this->entityManager->flush();

        return ApiResponder::success(EntityTransformer::activity($activity), JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $activity = $this->activityRepository->find($id);
        if ($activity === null) {
            return $this->notFound('Activity');
        }

        $payload = $this->parseJson($request);

        $error = $this->hydrate($activity, $payload);
        if ($error !== null) {
            return $error;
        }

        $violations = $this->validator->validate($activity);
        if (count($violations) > 0) {
            return $this->validationError($violations);
        }

        $this->entityManager->flush();

        return ApiResponder::success(EntityTransformer::activity($activity));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $activity = $this->activityRepository->find($id);
        if ($activity === null) {
            return $this->notFound('Activity');
        }

        $this->entityManager->remove($activity);
        $this->entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /** @param array<string, mixed> $payload */
    private function hydrate(Activity $activity, array $payload): ?JsonResponse
    {
        if (array_key_exists('dayId', $payload)) {
            $day = $this->dayRepository->find((int) $payload['dayId']);
            if ($day === null) {
                return ApiResponder::error('Day not found', 'RELATED_NOT_FOUND', JsonResponse::HTTP_UNPROCESSABLE_ENTITY, ['dayId' => 'Day not found']);
            }
            $activity->setDay($day);
        }

        if (array_key_exists('placeId', $payload)) {
            if ($payload['placeId'] === null) {
                $activity->setPlace(null);
            } else {
                $place = $this->placeRepository->find((int) $payload['placeId']);
                if ($place === null) {
                    return ApiResponder::error('Place not found', 'RELATED_NOT_FOUND', JsonResponse::HTTP_UNPROCESSABLE_ENTITY, ['placeId' => 'Place not found']);
                }
                $activity->setPlace($place);
            }
        }

        if (array_key_exists('title', $payload)) {
            $activity->setTitle((string) $payload['title']);
        }

        if (array_key_exists('category', $payload)) {
            $activity->setCategory((string) $payload['category']);
        }

        if (array_key_exists('startTime', $payload)) {
            $activity->setStartTime($payload['startTime'] !== null ? new \DateTimeImmutable('1970-01-01 ' . (string) $payload['startTime']) : null);
        }

        if (array_key_exists('endTime', $payload)) {
            $activity->setEndTime($payload['endTime'] !== null ? new \DateTimeImmutable('1970-01-01 ' . (string) $payload['endTime']) : null);
        }

        if (array_key_exists('status', $payload) && $payload['status'] !== null) {
            $status = ActivityStatus::tryFrom((string) $payload['status']);
            if ($status === null) {
                return ApiResponder::error('Invalid activity status', 'BAD_REQUEST', JsonResponse::HTTP_BAD_REQUEST, ['status' => 'Invalid enum value']);
            }
            $activity->setStatus($status);
        }

        if (array_key_exists('price', $payload)) {
            $activity->setPrice($payload['price'] !== null ? (string) $payload['price'] : null);
        }

        if (array_key_exists('currency', $payload)) {
            $activity->setCurrency($payload['currency'] !== null ? (string) $payload['currency'] : null);
        }

        if (array_key_exists('bookingCode', $payload)) {
            $activity->setBookingCode($payload['bookingCode'] !== null ? (string) $payload['bookingCode'] : null);
        }

        if (array_key_exists('notes', $payload)) {
            $activity->setNotes($payload['notes'] !== null ? (string) $payload['notes'] : null);
        }

        return null;
    }
}
