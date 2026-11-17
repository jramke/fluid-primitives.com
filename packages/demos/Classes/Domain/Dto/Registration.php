<?php

declare(strict_types=1);

namespace FluidPrimitives\Demos\Domain\Dto;

/**
 * The argument of `RegistrationController::createAction()`. Extbase fills a class without setters through its
 * constructor.
 */
final readonly class Registration
{
    public function __construct(
        public string $name = '',
        public string $email = '',
        #[\SensitiveParameter]
        public string $password = '',
        #[\SensitiveParameter]
        public string $passwordConfirmation = '',
    ) {}
}
