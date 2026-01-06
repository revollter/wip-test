<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Constraint to validate that reservation is not in the past.
 *
 * @Annotation
 */
#[\Attribute]
class NotInPast extends Constraint
{
    public string $message = 'Cannot create a reservation in the past.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
