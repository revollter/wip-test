<?php

namespace App\Validator;

use App\Entity\Reservation;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class NotInPastValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof NotInPast) {
            throw new UnexpectedTypeException($constraint, NotInPast::class);
        }

        if (!$value instanceof Reservation) {
            throw new UnexpectedValueException($value, Reservation::class);
        }

        if ($value->getDate() === null || $value->getStartTime() === null) {
            return;
        }

        $now = new \DateTime();
        $today = new \DateTime('today');
        $reservationDate = $value->getDate();

        // Date is before today
        if ($reservationDate < $today) {
            $this->context->buildViolation($constraint->message)
                ->atPath('date')
                ->addViolation();
            return;
        }

        // Date is today, check if start time is in the past
        if ($reservationDate->format('Y-m-d') === $today->format('Y-m-d')) {
            $startDateTime = \DateTime::createFromFormat(
                'Y-m-d H:i',
                $reservationDate->format('Y-m-d') . ' ' . $value->getStartTime()->format('H:i')
            );

            if ($startDateTime < $now) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('startTime')
                    ->addViolation();
            }
        }
    }
}
