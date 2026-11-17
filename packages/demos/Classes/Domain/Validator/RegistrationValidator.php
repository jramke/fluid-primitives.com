<?php

declare(strict_types=1);

namespace FluidPrimitives\Demos\Domain\Validator;

use FluidPrimitives\Demos\Domain\Dto\Registration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * The same rules as the fields in the template, which only help the visitor: the server checks them again.
 */
final class RegistrationValidator extends AbstractValidator
{
    public const int MIN_PASSWORD_LENGTH = 10;

    protected function isValid(mixed $value): void
    {
        if (!$value instanceof Registration) {
            $this->addError('Expected a registration.', 1763337600);
            return;
        }

        if (trim($value->name) === '') {
            $this->addErrorForProperty('name', 'Please enter your name.', 1763337601);
        }
        if (!GeneralUtility::validEmail($value->email)) {
            $this->addErrorForProperty('email', 'Please enter a valid email address.', 1763337602);
        }
        if (mb_strlen($value->password) < self::MIN_PASSWORD_LENGTH) {
            $this->addErrorForProperty(
                'password',
                sprintf('Use at least %d characters.', self::MIN_PASSWORD_LENGTH),
                1763337603,
            );
        }
        if (!hash_equals($value->password, $value->passwordConfirmation)) {
            $this->addErrorForProperty('passwordConfirmation', 'The passwords do not match.', 1763337604);
        }
    }
}
