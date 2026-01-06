<?php

namespace App\Validator;

use App\Entity\Reservation;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class EndTimeAfterStartTimeValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof EndTimeAfterStartTime) {
            throw new UnexpectedTypeException($constraint, EndTimeAfterStartTime::class);
        }

        if (!$value instanceof Reservation) {
            throw new UnexpectedValueException($value, Reservation::class);
        }

        if ($value->getStartTime() === null || $value->getEndTime() === null) {
            return;
        }

        if ($value->getEndTime() <= $value->getStartTime()) {
            $this->context->buildViolation($constraint->message)
                ->atPath('endTime')
                ->addViolation();
        }
    }
}
