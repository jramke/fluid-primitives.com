<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Domain\Model;

use TYPO3\CMS\Extbase\Attribute\Validate;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class EventRegistrationPerson extends AbstractEntity
{
    #[Validate(validator: 'String')]
    #[Validate(validator: 'NotEmpty')]
    #[Validate(validator: 'StringLength', options: ['maximum' => 255])]
    protected string $name = '';

    #[Validate(validator: 'EmailAddress')]
    #[Validate(validator: 'NotEmpty')]
    #[Validate(validator: 'StringLength', options: ['maximum' => 255])]
    protected string $email = '';

    #[Validate(validator: 'String')]
    #[Validate(validator: 'StringLength', options: ['maximum' => 255])]
    protected string $phone = '';

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }
}
