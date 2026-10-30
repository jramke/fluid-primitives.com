<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Services;

/**
 * Turns every `##` section of a rendered page into a `<details>`, so a long page like the changelog opens on its
 * newest entries and keeps the older ones one click away. It works on the HTML, after the Markdown is converted:
 * the `.md` request path never runs it, so agents read the plain Markdown.
 *
 * The heading stays an `<h2>` (inside the `<summary>`), so it keeps its id and its entry in the table of contents.
 * A line right below it that is only an emphasised date (`_2026-10-30_`) moves into the summary as a `<time>`.
 */
final class CollapsibleSections
{
    private const string HEADING_PATTERN = '/<h2 id="([^"]+)">(.+?)<a href="#\1" class="heading-permalink" title=""><\/a><\/h2>/s';

    private const string DATE_PATTERN = '/^\s*<p><em>(\d{4}-\d{2}-\d{2})<\/em><\/p>\s*/';

    /**
     * @param string $unreleasedTitle The first section with this title does not count as a release: the newest
     *     release after it opens too, because that is what the reader came to see next to it.
     */
    public static function wrap(string $html, string $unreleasedTitle = 'Unreleased'): string
    {
        // [intro, id, title, body, id, title, body, ...]
        $parts = preg_split(self::HEADING_PATTERN, $html, flags: PREG_SPLIT_DELIM_CAPTURE) ?: [$html];

        $wrapped = array_shift($parts);
        $titles = [];
        foreach (array_chunk($parts, length: 3) as $index => [$id, $title, $body]) {
            $titles[] = $title;
            $open = $index === 0 || $index === 1 && $titles[0] === $unreleasedTitle;
            $wrapped .= self::section($id, $title, $body, $open);
        }

        return $wrapped;
    }

    private static function section(string $id, string $title, string $body, bool $open): string
    {
        $time = '';
        $matches = [];
        if (preg_match(self::DATE_PATTERN, $body, $matches) === 1) {
            $time = '<time datetime="' . $matches[1] . '">' . $matches[1] . '</time>';
            $body = substr($body, strlen($matches[0]));
        }

        return (
            '<details class="collapsible-section"' .
            ($open ? ' open' : '') .
            '><summary><h2 id="' .
            $id .
            '">' .
            $title .
            '</h2>' .
            $time .
            '</summary><div>' .
            $body .
            '</div></details>'
        );
    }
}
