<?php

declare(strict_types=1);

namespace FluidPrimitives\Demos\Domain\Dto;

/**
 * The argument of `AccountController::updateAction()`.
 */
final readonly class AccountSettings
{
    public function __construct(
        public string $name = '',
    ) {}
}
