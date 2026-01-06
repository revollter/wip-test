<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Constraint to validate that end time is after start time.
 *
 * @Annotation
 */
#[\Attribute]
class EndTimeAfterStartTime extends Constraint
{
    public string $message = 'End time must be after start time.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
