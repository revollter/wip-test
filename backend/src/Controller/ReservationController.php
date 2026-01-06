<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Helper\FormHelper;
use App\Message\ReservationCreatedMessage;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * REST API controller for managing reservations.
 */
#[Route('/api/reservations')]
class ReservationController extends AbstractController
{
    public function __construct(
        private readonly ReservationRepository $reservationRepository,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    /**
     * Get all reservations with optional filters.
     */
    #[Route('', name: 'api_reservations_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $reservations = $this->reservationRepository->findByFilters(
            $request->query->get('roomId') ? (int) $request->query->get('roomId') : null,
            $request->query->get('startDate'),
            $request->query->get('endDate'),
            $request->query->get('date')
        );

        return $this->json([
            'data' => array_map(fn(Reservation $r) => $r->toArray(), $reservations),
            'total' => count($reservations),
        ]);
    }

    /**
     * Get a single reservation by ID.
     */
    #[Route('/{id}', name: 'api_reservations_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): JsonResponse
    {
        $reservation = $this->reservationRepository->find($id);

        if (!$reservation) {
            return $this->json([
                'error' => 'Reservation not found',
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'data' => $reservation->toArray(),
        ]);
    }

    /**
     * Create a new reservation.
     * Validates for time conflicts and dispatches notification to RabbitMQ.
     */
    #[Route('', name: 'api_reservations_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->json([
                'error' => 'Invalid JSON payload',
            ], Response::HTTP_BAD_REQUEST);
        }

        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->submit($data);

        if (!$form->isValid()) {
            return $this->json([
                'error' => 'Validation failed',
                'details' => FormHelper::getErrors($form),
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->reservationRepository->save($reservation);

        // Dispatch notification message to RabbitMQ
        $message = new ReservationCreatedMessage(
            $reservation->getId(),
            $reservation->getConferenceRoom()->getName(),
            $reservation->getReserverName(),
            $reservation->getDate()->format('Y-m-d'),
            $reservation->getStartTime()->format('H:i'),
            $reservation->getEndTime()->format('H:i')
        );
        $this->messageBus->dispatch($message);

        return $this->json([
            'message' => 'Reservation created successfully',
            'data' => $reservation->toArray(),
        ], Response::HTTP_CREATED);
    }

    /**
     * Update an existing reservation.
     */
    #[Route('/{id}', name: 'api_reservations_update', methods: ['PUT', 'PATCH'], requirements: ['id' => '\d+'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $reservation = $this->reservationRepository->find($id);

        if (!$reservation) {
            return $this->json([
                'error' => 'Reservation not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->json([
                'error' => 'Invalid JSON payload',
            ], Response::HTTP_BAD_REQUEST);
        }

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->submit($data, $request->getMethod() === 'PATCH');

        if (!$form->isValid()) {
            return $this->json([
                'error' => 'Validation failed',
                'details' => FormHelper::getErrors($form),
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->reservationRepository->save($reservation);

        return $this->json([
            'message' => 'Reservation updated successfully',
            'data' => $reservation->toArray(),
        ]);
    }

    /**
     * Delete a reservation.
     */
    #[Route('/{id}', name: 'api_reservations_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(int $id): JsonResponse
    {
        $reservation = $this->reservationRepository->find($id);

        if (!$reservation) {
            return $this->json([
                'error' => 'Reservation not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $this->reservationRepository->remove($reservation);

        return $this->json([
            'message' => 'Reservation deleted successfully',
        ]);
    }
}
