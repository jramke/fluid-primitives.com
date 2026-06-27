<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Domain\Validator;

use FluidPrimitives\Docs\Domain\Model\EventRegistration;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

final class EventRegistrationValidator extends AbstractValidator
{
    protected function isValid(mixed $value): void
    {
        if (!$value instanceof EventRegistration) {
            // addError will result in a full form error in the frontend
            $this->addError(
                'The ' .
                self::class .
                ' can only handle classes of type ' .
                EventRegistration::class .
                '. ' .
                $value::class .
                ' given instead.',
                1782582413,
            );
        }

        if ($this->needsStudentId($value) && $value->getStudentId() === '') {
            // addErrorForProperty will result in a field error in the frontend
            $this->addErrorForProperty(
                'studentId',
                'You need to provide a student id for the student ticket',
                1782582859,
            );
        }
    }

    private function needsStudentId(EventRegistration $eventRegistration): bool
    {
        return $eventRegistration->getTicketType() === 'student';
    }
}
