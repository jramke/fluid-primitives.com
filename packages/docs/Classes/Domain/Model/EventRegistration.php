<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Domain\Model;

use TYPO3\CMS\Extbase\Attribute\Validate;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class EventRegistration extends AbstractEntity
{
    #[Validate(validator: 'String')]
    #[Validate(validator: 'NotEmpty')]
    #[Validate(validator: 'RegularExpression', options: ['regularExpression' => '/^(vip|standard|student)$/'])]
    protected string $ticketType = '';

    #[Validate(validator: 'NumberRange', options: ['minimum' => 1, 'maximum' => 10])]
    protected int $ticketCount = 1;

    protected EventRegistrationPerson $person;

    #[Validate(validator: 'String')]
    #[Validate(validator: 'NotEmpty')]
    #[Validate(validator: 'RegularExpression', options: ['regularExpression' => '/^(person|virtual)$/'])]
    protected string $mode = '';

    #[Validate(validator: 'String')]
    protected string $studentId = '';

    #[Validate(validator: 'Collection', options: ['elementValidator' => 'String'])]
    /** @var array<string> */
    protected array $a11yNeeds = [];

    #[Validate(validator: 'Text')]
    #[Validate(validator: 'StringLength', options: ['maximum' => 500])]
    protected string $comment = '';

    #[Validate(validator: 'Boolean', options: ['is' => true])]
    protected bool $privacy = false;

    public function getTicketType(): string
    {
        return $this->ticketType;
    }

    public function setTicketType(string $ticketType): void
    {
        $this->ticketType = $ticketType;
    }

    public function getTicketCount(): int
    {
        return $this->ticketCount;
    }

    public function setTicketCount(int $ticketCount): void
    {
        $this->ticketCount = $ticketCount;
    }

    public function getPerson(): EventRegistrationPerson
    {
        return $this->person;
    }

    public function setPerson(EventRegistrationPerson $person): void
    {
        $this->person = $person;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }

    public function getStudentId(): string
    {
        return $this->studentId;
    }

    public function setStudentId(string $studentId): void
    {
        $this->studentId = $studentId;
    }

    /** @return array<string> */
    public function getA11yNeeds(): array
    {
        return $this->a11yNeeds;
    }

    /** @param array<string> $a11yNeeds */
    public function setA11yNeeds(array $a11yNeeds): void
    {
        $this->a11yNeeds = $a11yNeeds;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    public function getPrivacy(): bool
    {
        return $this->privacy;
    }

    public function setPrivacy(bool $privacy): void
    {
        $this->privacy = $privacy;
    }
}
