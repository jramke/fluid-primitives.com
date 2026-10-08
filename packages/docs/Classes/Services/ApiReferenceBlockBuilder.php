<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Services;

use Jramke\FluidPrimitives\Utility\Typed;

/**
 * Turns the generated JSON of one client export (see `GeneratedDocsMetadata::forSymbol()`) into a flat list
 * of blocks, so the `ui:apiReference` template only has to know how to draw the four kinds of
 * `ApiReferenceBlock`. The page author writes the heading above the shortcode, hence the blocks start at
 * level 3.
 */
final class ApiReferenceBlockBuilder
{
    private const array DEFAULTS = [
        'kind' => '',
        'name' => '',
        'package' => '',
        'summary' => '',
        'deprecated' => '',
        'declaration' => '',
        'signature' => '',
        'title' => '',
        'examples' => [],
        'constructorParams' => [],
        'properties' => [],
        'methods' => [],
        'members' => [],
        'signatures' => [],
        'params' => [],
        'returns' => [],
    ];

    /**
     * @param array<array-key, mixed> $entry
     * @return list<array<string, mixed>>
     */
    public static function build(array $entry): array
    {
        $entry = [...self::DEFAULTS, ...$entry];
        $kind = Typed::string($entry['kind']);

        $blocks = match ($kind) {
            'external' => [
                ApiReferenceBlock::text(sprintf(
                    '`%s` is re-exported from `%s`, see its documentation.',
                    Typed::string($entry['name']),
                    Typed::string($entry['package']),
                )),
            ],
            'class' => [
                ...self::intro($entry),
                ApiReferenceBlock::code(Typed::string($entry['declaration'])),
                ...self::examples($entry),
                ...self::section('Constructor', $entry['constructorParams']),
                ...self::section('Properties', $entry['properties'], full: false),
                ...self::methods($entry['methods'], level: 4, heading: 'Methods'),
            ],
            'function' => self::callable($entry),
            'object' => [
                ...self::intro($entry),
                ...self::examples($entry),
                ...self::methods($entry['methods'], level: 3),
            ],
            default => [
                ...self::intro($entry),
                ApiReferenceBlock::code(Typed::string($entry['declaration'])),
                ...self::examples($entry),
                ApiReferenceBlock::table(self::rows($entry['members'])),
            ],
        };

        return array_values(array_filter($blocks));
    }

    /**
     * A function, or one method of a class or object.
     *
     * @param array<array-key, mixed> $entry
     * @return list<array<string, mixed>>
     */
    private static function callable(array $entry): array
    {
        $blocks = self::intro($entry);

        foreach (self::rows($entry['signatures']) as $signature) {
            $signature = [...self::DEFAULTS, ...$signature];
            $returns = ['type' => '', 'description' => '', ...self::rows([$signature['returns']])[0]];
            $returnsText = Typed::string($returns['description']);

            $blocks[] = ApiReferenceBlock::code(Typed::string($signature['signature']));
            $blocks[] = ApiReferenceBlock::table(self::rows($signature['params']));
            $blocks[] = ApiReferenceBlock::text(
                $returnsText === ''
                    ? ''
                    : sprintf('**Returns** `%s`. %s', Typed::string($returns['type']), $returnsText),
            );
        }

        return [...$blocks, ...self::examples($entry)];
    }

    /**
     * The summary and the deprecation notice, which every kind of entry can have.
     *
     * @param array<array-key, mixed> $entry
     * @return list<array<string, mixed>>
     */
    private static function intro(array $entry): array
    {
        $deprecated = Typed::string($entry['deprecated']);

        return [
            ApiReferenceBlock::text(Typed::string($entry['summary'])),
            ApiReferenceBlock::text($deprecated === '' ? '' : "**Deprecated.** {$deprecated}"),
        ];
    }

    /**
     * @param array<array-key, mixed> $entry
     * @return list<array<string, mixed>>
     */
    private static function examples(array $entry): array
    {
        return array_map(static fn(array $example): array => ApiReferenceBlock::code(
            Typed::string($example['code'] ?? ''),
            Typed::string($example['language'] ?? 'typescript'),
        ), self::rows($entry['examples']));
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function section(string $title, mixed $items, bool $full = true): array
    {
        $table = ApiReferenceBlock::table(self::rows($items), $full);

        return $table === [] ? [] : [ApiReferenceBlock::heading(3, $title), $table];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function methods(mixed $methods, int $level, string $heading = ''): array
    {
        $methods = self::rows($methods);
        $blocks = $methods !== [] && $heading !== '' ? [ApiReferenceBlock::heading(3, $heading)] : [];

        foreach ($methods as $method) {
            $method = [...self::DEFAULTS, ...$method];
            $blocks[] = ApiReferenceBlock::heading($level, Typed::string($method['title']));
            array_push($blocks, ...self::callable($method));
        }

        return $blocks;
    }

    /**
     * The decoded JSON has no static shape: keep what is a list of arrays.
     *
     * @return list<array<array-key, mixed>>
     */
    private static function rows(mixed $value): array
    {
        return array_values(array_filter(Typed::arrayOrNull($value) ?? [], is_array(...)));
    }
}
