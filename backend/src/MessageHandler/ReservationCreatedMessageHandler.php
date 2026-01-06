<?php

namespace App\MessageHandler;

use App\Message\ReservationCreatedMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Handler for ReservationCreatedMessage.
 * Processes new reservation notifications from RabbitMQ.
 */
#[AsMessageHandler]
class ReservationCreatedMessageHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(ReservationCreatedMessage $message): void
    {
        // Log the reservation notification
        $this->logger->info('Processing new reservation notification', [
            'reservationId' => $message->getReservationId(),
            'roomName' => $message->getRoomName(),
            'reserverName' => $message->getReserverName(),
            'date' => $message->getDate(),
            'startTime' => $message->getStartTime(),
            'endTime' => $message->getEndTime(),
        ]);

        // TODO implement additional notification logic

        $this->logger->info(sprintf(
            'Reservation notification processed: %s reserved %s on %s from %s to %s',
            $message->getReserverName(),
            $message->getRoomName(),
            $message->getDate(),
            $message->getStartTime(),
            $message->getEndTime()
        ));
    }
}
