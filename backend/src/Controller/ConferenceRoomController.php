<?php

namespace App\Controller;

use App\Entity\ConferenceRoom;
use App\Form\ConferenceRoomType;
use App\Helper\FormHelper;
use App\Repository\ConferenceRoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * REST API controller for managing conference rooms.
 */
#[Route('/api/rooms')]
class ConferenceRoomController extends AbstractController
{
    public function __construct(
        private readonly ConferenceRoomRepository $roomRepository,
    ) {
    }

    /**
     * Get all conference rooms.
     */
    #[Route('', name: 'api_rooms_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $rooms = $this->roomRepository->findAllOrderedByName();

        return $this->json([
            'data' => array_map(fn(ConferenceRoom $room) => $room->toArray(), $rooms),
            'total' => count($rooms),
        ]);
    }

    /**
     * Get a single conference room by ID.
     */
    #[Route('/{id}', name: 'api_rooms_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): JsonResponse
    {
        $room = $this->roomRepository->find($id);

        if (!$room) {
            return $this->json([
                'error' => 'Conference room not found',
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'data' => $room->toArray(),
        ]);
    }

    /**
     * Create a new conference room.
     */
    #[Route('', name: 'api_rooms_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->json([
                'error' => 'Invalid JSON payload',
            ], Response::HTTP_BAD_REQUEST);
        }

        $room = new ConferenceRoom();
        $form = $this->createForm(ConferenceRoomType::class, $room);
        $form->submit($data);

        if (!$form->isValid()) {
            return $this->json([
                'error' => 'Validation failed',
                'details' => FormHelper::getErrors($form),
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->roomRepository->save($room);

        return $this->json([
            'message' => 'Conference room created successfully',
            'data' => $room->toArray(),
        ], Response::HTTP_CREATED);
    }

    /**
     * Update an existing conference room.
     */
    #[Route('/{id}', name: 'api_rooms_update', methods: ['PUT', 'PATCH'], requirements: ['id' => '\d+'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $room = $this->roomRepository->find($id);

        if (!$room) {
            return $this->json([
                'error' => 'Conference room not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->json([
                'error' => 'Invalid JSON payload',
            ], Response::HTTP_BAD_REQUEST);
        }

        $form = $this->createForm(ConferenceRoomType::class, $room);
        $form->submit($data, $request->getMethod() === 'PATCH');

        if (!$form->isValid()) {
            return $this->json([
                'error' => 'Validation failed',
                'details' => FormHelper::getErrors($form),
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->roomRepository->save($room);

        return $this->json([
            'message' => 'Conference room updated successfully',
            'data' => $room->toArray(),
        ]);
    }

    /**
     * Delete a conference room.
     */
    #[Route('/{id}', name: 'api_rooms_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(int $id): JsonResponse
    {
        $room = $this->roomRepository->find($id);

        if (!$room) {
            return $this->json([
                'error' => 'Conference room not found',
            ], Response::HTTP_NOT_FOUND);
        }

        if (!$room->getReservations()->isEmpty()) {
            return $this->json([
                'error' => 'Cannot delete room with existing reservations',
                'reservationCount' => $room->getReservations()->count(),
            ], Response::HTTP_CONFLICT);
        }

        $this->roomRepository->remove($room);

        return $this->json([
            'message' => 'Conference room deleted successfully',
        ]);
    }
}
