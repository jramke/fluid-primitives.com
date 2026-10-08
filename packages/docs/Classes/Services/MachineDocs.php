<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Services;

use FluidPrimitives\Docs\Utility\GeneratedDocsMetadata;
use Jramke\FluidPrimitives\Utility\Typed;

/**
 * What `ui:componentPropsTable` shows of the client side of a primitive - the machine JS API, its options,
 * the data attributes per part and the keyboard table - read from the generated docs data. A self-made
 * machine (`machines/`) has Markdown descriptions, which become HTML for the page; the ones of a Zag.js
 * machine stay as they are.
 */
final readonly class MachineDocs
{
    /**
     * @param array<array-key, mixed> $metadata
     */
    public function __construct(
        private array $metadata,
        private bool $markdown,
    ) {}

    public static function forPrimitive(string $primitive, bool $markdown): self
    {
        return new self($primitive === '' ? [] : GeneratedDocsMetadata::forMachine($primitive), $markdown);
    }

    public function isMachineSource(): bool
    {
        return ($this->metadata['source'] ?? null) === GeneratedDocsMetadata::SOURCE_MACHINE;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function accessibility(): array
    {
        $accessibility = Typed::arrayOrNull($this->metadata['accessibility'] ?? null) ?? [];

        return Typed::arrayOrNull($accessibility['keyboard'] ?? null) ?? [];
    }

    /**
     * @return array<array-key, mixed>
     */
    public function api(): array
    {
        return $this->descriptions(Typed::arrayOrNull($this->apiSection()['api'] ?? null) ?? []);
    }

    /**
     * The options of `new Form({...})`. Only for a self-made machine: those of a Zag.js machine are documented
     * upstream, which the page links to.
     *
     * @return array<array-key, mixed>
     */
    public function options(): array
    {
        if (!$this->isMachineSource()) {
            return [];
        }

        $options = $this->descriptions(Typed::arrayOrNull($this->apiSection()['context'] ?? null) ?? []);

        return array_map(static fn(mixed $option): mixed => (
            is_array($option) ? ['defaultValue' => '-', ...$option] : $option
        ), $options);
    }

    /**
     * @return array<array-key, mixed>
     */
    public function dataAttributes(string $partName): array
    {
        $attributes = Typed::arrayOrNull($this->metadata['dataAttributes'] ?? null) ?? [];
        $part = str_replace(
            ' ',
            replace: '',
            subject: ucwords((string)preg_replace('/[^a-zA-Z0-9]+/', replacement: ' ', subject: $partName)),
        );

        return Typed::arrayOrNull($attributes[$part] ?? null) ?? [];
    }

    /**
     * @return array<array-key, mixed>
     */
    private function apiSection(): array
    {
        return Typed::arrayOrNull($this->metadata['api'] ?? null) ?? [];
    }

    /**
     * @param array<array-key, mixed> $entries
     * @return array<array-key, mixed>
     */
    private function descriptions(array $entries): array
    {
        if (!$this->isMachineSource() || $this->markdown) {
            return $entries;
        }

        return array_map(static function (mixed $entry): mixed {
            return (
                is_array($entry)
                    ? [
                        ...$entry,
                        'description' => GeneratedDocsMarkdown::toHtml(Typed::string($entry['description'] ?? '')),
                    ]
                    : $entry
            );
        }, $entries);
    }
}
