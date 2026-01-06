<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Constraint to validate that a reservation does not overlap with existing reservations.
 *
 * @Annotation
 */
#[\Attribute]
class NoOverlappingReservation extends Constraint
{
    public string $message = 'The selected time slot conflicts with an existing reservation.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
