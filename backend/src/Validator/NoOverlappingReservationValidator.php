<?php

namespace App\Validator;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class NoOverlappingReservationValidator extends ConstraintValidator
{
    public function __construct(
        private readonly ReservationRepository $reservationRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof NoOverlappingReservation) {
            throw new UnexpectedTypeException($constraint, NoOverlappingReservation::class);
        }

        if (!$value instanceof Reservation) {
            throw new UnexpectedValueException($value, Reservation::class);
        }

        if ($value->getConferenceRoom() === null
            || $value->getDate() === null
            || $value->getStartTime() === null
            || $value->getEndTime() === null
        ) {
            return;
        }

        $hasOverlap = $this->reservationRepository->hasOverlappingReservation(
            $value->getConferenceRoom(),
            $value->getDate(),
            $value->getStartTime(),
            $value->getEndTime(),
            $value->getId()
        );

        if ($hasOverlap) {
            $this->context->buildViolation($constraint->message)
                ->atPath('startTime')
                ->addViolation();
        }
    }
}
