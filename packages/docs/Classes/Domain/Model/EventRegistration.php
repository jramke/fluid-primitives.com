<?php

namespace FluidPrimitives\Docs\Domain\Model;

use TYPO3\CMS\Extbase\Annotation\Validate;

class EventRegistration
{
    #[Validate(['validator' => 'String'])]
    #[Validate(['validator' => 'NotEmpty'])]
    #[Validate([
        'validator' => 'RegularExpression',
        'options' => ['regularExpression' => '/^(vip|standard|student)$/']
    ])]
    private string $ticketType = '';

    #[Validate([
        'validator' => 'NumberRange',
        'options' => ['minimum' => 1, 'maximum' => 10],
    ])]
    private int $ticketCount = 1;

    #[Validate(['validator' => 'String'])]
    #[Validate(['validator' => 'NotEmpty'])]
    #[Validate([
        'validator' => 'StringLength',
        'options' => ['maximum' => 255],
    ])]
    private string $name = '';

    #[Validate(['validator' => 'EmailAddress'])]
    #[Validate(['validator' => 'NotEmpty'])]
    #[Validate([
        'validator' => 'StringLength',
        'options' => ['maximum' => 255],
    ])]
    private string $email = '';

    #[Validate(['validator' => 'String'])]
    #[Validate([
        'validator' => 'StringLength',
        'options' => ['maximum' => 255],
    ])]
    private string $phone = '';

    #[Validate(['validator' => 'String'])]
    #[Validate(['validator' => 'NotEmpty'])]
    #[Validate([
        'validator' => 'RegularExpression',
        'options' => ['regularExpression' => '/^(person|virtual)$/']
    ])]
    private string $mode = '';

    #[Validate([
        'validator' => 'Collection',
        'options' => ['elementValidator' => 'String'],
    ])]
    /** @var array<string> */
    private array $a11yNeeds = [];

    #[Validate(['validator' => 'Text'])]
    #[Validate([
        'validator' => 'StringLength',
        'options' => ['maximum' => 500],
    ])]
    private string $comment = '';

    #[Validate([
        'validator' => 'Boolean',
        'options' => ['is' => true],
    ])]
    private bool $privacy = false;

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

    public function getMode(): string
    {
        return $this->mode;
    }
    public function setMode(string $mode): void
    {
        $this->mode = $mode;
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
