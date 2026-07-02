<?php

declare(strict_types=1);

namespace App\Api;

use App\Entity\Activity;
use App\Entity\Day;
use App\Entity\Place;
use App\Entity\Ticket;
use App\Entity\Trip;

final class EntityTransformer
{
    /** @return array<string, mixed> */
    public static function trip(Trip $trip): array
    {
        return [
            'id' => $trip->getId(),
            'name' => $trip->getName(),
            'city' => $trip->getCity(),
            'startDate' => $trip->getStartDate()?->format('Y-m-d'),
            'endDate' => $trip->getEndDate()?->format('Y-m-d'),
            'currency' => $trip->getCurrency(),
            'notes' => $trip->getNotes(),
            'createdAt' => $trip->getCreatedAt()?->format(DATE_ATOM),
            'updatedAt' => $trip->getUpdatedAt()?->format(DATE_ATOM),
        ];
    }

    /** @return array<string, mixed> */
    public static function day(Day $day): array
    {
        return [
            'id' => $day->getId(),
            'tripId' => $day->getTrip()?->getId(),
            'date' => $day->getDate()?->format('Y-m-d'),
            'title' => $day->getTitle(),
            'notes' => $day->getNotes(),
            'weatherTip' => $day->getWeatherTip(),
            'district' => $day->getDistrict(),
        ];
    }

    /** @return array<string, mixed> */
    public static function place(Place $place): array
    {
        return [
            'id' => $place->getId(),
            'name' => $place->getName(),
            'type' => $place->getType()?->value,
            'address' => $place->getAddress(),
            'lat' => $place->getLat(),
            'lng' => $place->getLng(),
            'priceLevel' => $place->getPriceLevel(),
            'averagePrice' => $place->getAveragePrice() !== null ? (float) $place->getAveragePrice() : null,
            'currency' => $place->getCurrency(),
            'website' => $place->getWebsite(),
            'phone' => $place->getPhone(),
            'notes' => $place->getNotes(),
        ];
    }

    /** @return array<string, mixed> */
    public static function activity(Activity $activity): array
    {
        return [
            'id' => $activity->getId(),
            'dayId' => $activity->getDay()?->getId(),
            'placeId' => $activity->getPlace()?->getId(),
            'title' => $activity->getTitle(),
            'category' => $activity->getCategory(),
            'startTime' => $activity->getStartTime()?->format('H:i:s'),
            'endTime' => $activity->getEndTime()?->format('H:i:s'),
            'status' => $activity->getStatus()?->value,
            'price' => $activity->getPrice() !== null ? (float) $activity->getPrice() : null,
            'currency' => $activity->getCurrency(),
            'bookingCode' => $activity->getBookingCode(),
            'notes' => $activity->getNotes(),
        ];
    }

    /** @return array<string, mixed> */
    public static function ticket(Ticket $ticket): array
    {
        return [
            'id' => $ticket->getId(),
            'dayId' => $ticket->getDay()?->getId(),
            'activityId' => $ticket->getActivity()?->getId(),
            'type' => $ticket->getType()?->value,
            'title' => $ticket->getTitle(),
            'provider' => $ticket->getProvider(),
            'code' => $ticket->getCode(),
            'holder' => $ticket->getHolder(),
            'seat' => $ticket->getSeat(),
            'gate' => $ticket->getGate(),
            'price' => $ticket->getPrice() !== null ? (float) $ticket->getPrice() : null,
            'currency' => $ticket->getCurrency(),
            'documentUrl' => $ticket->getDocumentUrl(),
            'notes' => $ticket->getNotes(),
        ];
    }
}
