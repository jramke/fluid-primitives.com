<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Services;

use Jramke\FluidPrimitives\Utility\Typed;

/**
 * The four kinds of block the `ui:apiReference` template draws - heading, text, code and table - as plain
 * arrays. A block without content is an empty array, so callers can spread the result unconditionally.
 */
final class ApiReferenceBlock
{
    /**
     * @return array{type: string, level: int, text: string}
     */
    public static function heading(int $level, string $text): array
    {
        return ['type' => 'heading', 'level' => $level, 'text' => $text];
    }

    /**
     * @return array<string, string>
     */
    public static function text(string $markdown): array
    {
        return $markdown === '' ? [] : ['type' => 'text', 'markdown' => $markdown];
    }

    /**
     * @return array<string, string>
     */
    public static function code(string $code, string $language = 'typescript'): array
    {
        return $code === '' ? [] : ['type' => 'code', 'code' => $code, 'language' => $language];
    }

    /**
     * One row per parameter, property or member - they all read as name, type, description, required, default.
     * A class property has no use for the last two, hence `$full`.
     *
     * @param list<array<array-key, mixed>> $items
     * @return array<string, mixed>
     */
    public static function table(array $items, bool $full = true): array
    {
        if ($items === []) {
            return [];
        }

        $rows = array_map(static function (array $item): array {
            $item = [
                'name' => '',
                'type' => '',
                'description' => '',
                'deprecated' => '',
                'default' => '',
                'optional' => false,
                ...$item,
            ];
            $deprecated = Typed::string($item['deprecated']);
            $default = Typed::string($item['default']);

            return [
                'name' => Typed::string($item['name']),
                'type' => Typed::string($item['type']),
                'description' => trim(
                    Typed::string($item['description']) . ($deprecated === '' ? '' : " **Deprecated.** {$deprecated}"),
                ),
                'required' => $item['optional'] === true ? 'No' : 'Yes',
                'default' => $default === '' ? '-' : $default,
            ];
        }, $items);

        return ['type' => 'table', 'full' => $full, 'rows' => $rows];
    }
}
