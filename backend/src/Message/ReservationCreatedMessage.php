<?php

namespace App\Message;

/**
 * Message dispatched when a new reservation is created.
 * This message will be sent to RabbitMQ for async processing.
 */
class ReservationCreatedMessage
{
    public function __construct(
        private readonly int $reservationId,
        private readonly string $roomName,
        private readonly string $reserverName,
        private readonly string $date,
        private readonly string $startTime,
        private readonly string $endTime,
    ) {
    }

    public function getReservationId(): int
    {
        return $this->reservationId;
    }

    public function getRoomName(): string
    {
        return $this->roomName;
    }

    public function getReserverName(): string
    {
        return $this->reserverName;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getStartTime(): string
    {
        return $this->startTime;
    }

    public function getEndTime(): string
    {
        return $this->endTime;
    }

    /**
     * Get message data as array for logging/debugging.
     */
    public function toArray(): array
    {
        return [
            'reservationId' => $this->reservationId,
            'roomName' => $this->roomName,
            'reserverName' => $this->reserverName,
            'date' => $this->date,
            'startTime' => $this->startTime,
            'endTime' => $this->endTime,
        ];
    }
}
