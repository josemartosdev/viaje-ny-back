<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\ApiResponder;
use App\Api\EntityTransformer;
use App\Entity\Ticket;
use App\Enum\TicketType;
use App\Repository\ActivityRepository;
use App\Repository\DayRepository;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/tickets')]
final class TicketController extends AbstractApiController
{
    public function __construct(
        private readonly TicketRepository $ticketRepository,
        private readonly DayRepository $dayRepository,
        private readonly ActivityRepository $activityRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $dayId = $request->query->getInt('dayId') ?: null;
        $type = $request->query->get('type');
        $parsedType = $type ? TicketType::tryFrom($type) : null;
        $items = $this->ticketRepository->findByFilters($dayId, $parsedType, $request->query->get('q'));

        return ApiResponder::success(array_map(static fn(Ticket $ticket): array => EntityTransformer::ticket($ticket), $items));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $ticket = $this->ticketRepository->find($id);
        if ($ticket === null) {
            return $this->notFound('Ticket');
        }

        return ApiResponder::success(EntityTransformer::ticket($ticket));
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $payload = $this->parseJson($request);

        $ticket = new Ticket();
        $error = $this->hydrate($ticket, $payload);
        if ($error !== null) {
            return $error;
        }

        $violations = $this->validator->validate($ticket);
        if (count($violations) > 0) {
            return $this->validationError($violations);
        }

        $this->entityManager->persist($ticket);
        $this->entityManager->flush();

        return ApiResponder::success(EntityTransformer::ticket($ticket), JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $ticket = $this->ticketRepository->find($id);
        if ($ticket === null) {
            return $this->notFound('Ticket');
        }

        $payload = $this->parseJson($request);

        $error = $this->hydrate($ticket, $payload);
        if ($error !== null) {
            return $error;
        }

        $violations = $this->validator->validate($ticket);
        if (count($violations) > 0) {
            return $this->validationError($violations);
        }

        $this->entityManager->flush();

        return ApiResponder::success(EntityTransformer::ticket($ticket));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $ticket = $this->ticketRepository->find($id);
        if ($ticket === null) {
            return $this->notFound('Ticket');
        }

        $this->entityManager->remove($ticket);
        $this->entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /** @param array<string, mixed> $payload */
    private function hydrate(Ticket $ticket, array $payload): ?JsonResponse
    {
        if (array_key_exists('dayId', $payload)) {
            $day = $this->dayRepository->find((int) $payload['dayId']);
            if ($day === null) {
                return ApiResponder::error('Day not found', 'RELATED_NOT_FOUND', JsonResponse::HTTP_UNPROCESSABLE_ENTITY, ['dayId' => 'Day not found']);
            }
            $ticket->setDay($day);
        }

        if (array_key_exists('activityId', $payload)) {
            if ($payload['activityId'] === null) {
                $ticket->setActivity(null);
            } else {
                $activity = $this->activityRepository->find((int) $payload['activityId']);
                if ($activity === null) {
                    return ApiResponder::error('Activity not found', 'RELATED_NOT_FOUND', JsonResponse::HTTP_UNPROCESSABLE_ENTITY, ['activityId' => 'Activity not found']);
                }
                $ticket->setActivity($activity);
            }
        }

        if (array_key_exists('type', $payload) && $payload['type'] !== null) {
            $type = TicketType::tryFrom((string) $payload['type']);
            if ($type === null) {
                return ApiResponder::error('Invalid ticket type', 'BAD_REQUEST', JsonResponse::HTTP_BAD_REQUEST, ['type' => 'Invalid enum value']);
            }
            $ticket->setType($type);
        }

        if (array_key_exists('title', $payload)) {
            $ticket->setTitle((string) $payload['title']);
        }

        if (array_key_exists('provider', $payload)) {
            $ticket->setProvider($payload['provider'] !== null ? (string) $payload['provider'] : null);
        }

        if (array_key_exists('code', $payload)) {
            $ticket->setCode($payload['code'] !== null ? (string) $payload['code'] : null);
        }

        if (array_key_exists('holder', $payload)) {
            $ticket->setHolder($payload['holder'] !== null ? (string) $payload['holder'] : null);
        }

        if (array_key_exists('seat', $payload)) {
            $ticket->setSeat($payload['seat'] !== null ? (string) $payload['seat'] : null);
        }

        if (array_key_exists('gate', $payload)) {
            $ticket->setGate($payload['gate'] !== null ? (string) $payload['gate'] : null);
        }

        if (array_key_exists('price', $payload)) {
            $ticket->setPrice($payload['price'] !== null ? (string) $payload['price'] : null);
        }

        if (array_key_exists('currency', $payload)) {
            $ticket->setCurrency($payload['currency'] !== null ? (string) $payload['currency'] : null);
        }

        if (array_key_exists('documentUrl', $payload)) {
            $ticket->setDocumentUrl($payload['documentUrl'] !== null ? (string) $payload['documentUrl'] : null);
        }

        if (array_key_exists('notes', $payload)) {
            $ticket->setNotes($payload['notes'] !== null ? (string) $payload['notes'] : null);
        }

        return null;
    }
}
