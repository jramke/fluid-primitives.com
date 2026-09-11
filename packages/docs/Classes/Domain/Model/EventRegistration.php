<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Domain\Model;

use TYPO3\CMS\Extbase\Attribute\FileUpload;
use TYPO3\CMS\Extbase\Attribute\Validate;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
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

    #[FileUpload(validation: [
        'fileSize' => ['maximum' => '5M'],
        'mimeType' => ['allowedMimeTypes' => ['image/jpeg', 'image/png']],
        'fileExtension' => ['allowedFileExtensions' => ['jpg', 'jpeg', 'png']],
    ], uploadFolder: '1:/user_upload/event_registrations/badge_photos/')]
    protected ?FileReference $badgePhoto = null;

    /**
     * KNOWN LIMITATION: this never round-trips through a read. Persistence writes it correctly as a
     * comma-separated string (Extbase's `getPlainValue()` implodes arrays for storage), but reading
     * it back (e.g. `$repository->findByUid()`, so an edit form can pre-check these boxes) always
     * yields an empty array, for two compounding reasons:
     * - Extbase's `DataMapper::thawProperties()` has no support at all for hydrating a plain
     *   `array`-typed property from a column (`'array' => null, // Not supported, yet!`).
     * - Renaming the backing property so it no longer collides with these `array`-typed accessors
     *   (tried: a `string $a11yNeedsRaw` + a `Configuration/Extbase/Persistence/Classes.php` column
     *   mapping) does make the DB value hydrate correctly, but then breaks *writing* instead: Extbase's
     *   `PersistentObjectConverter::getTypeOfChildProperty()` requires a property literally named
     *   `a11yNeeds` to exist on the class before it will map the submitted `a11yNeeds[]` checkbox
     *   values onto it at all, independent of that property's declared type.
     * Properly fixing this needs a custom `PropertyMappingConfiguration` (to point the converter at a
     * differently-named target property) rather than a plain property rename - out of scope for now.
     */
    #[Validate(validator: 'Collection', options: ['elementValidator' => 'String'])]
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

    public function getBadgePhoto(): ?FileReference
    {
        return $this->badgePhoto;
    }

    public function setBadgePhoto(?FileReference $badgePhoto): void
    {
        $this->badgePhoto = $badgePhoto;
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
