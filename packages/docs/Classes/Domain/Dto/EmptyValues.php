<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Domain\Dto;

use TYPO3\CMS\Core\Http\UploadedFile;

/**
 * The argument of `EmptyValuesController::dtoAction()`. Extbase fills a class without setters through
 * its constructor.
 */
final readonly class EmptyValues
{
    /**
     * @param list<string> $interests
     * @param list<string> $skills
     */
    public function __construct(
        public bool $newsletter = false,
        public bool $terms = false,
        public array $interests = [],
        public string $country = '',
        public array $skills = [],
        public ?\DateTime $birthday = null,
        public float $budget = 0,
        public string $plan = '',
        public ?UploadedFile $avatar = null,
    ) {}
}
