<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Services;

use Jramke\FluidPrimitives\Domain\Dto\TocItem;

/**
 * Cuts the rendered HTML of a page at its `##` headings, so a long page like the changelog can show each section as
 * an accordion item. It works on the HTML, after the Markdown is converted: the `.md` request path never runs it, so
 * agents read the plain Markdown.
 *
 * A line right below a heading that is only an emphasised date (`_2026-10-30_`) becomes the `date` of the section.
 */
final class PageSections
{
    private const string HEADING_PATTERN = '/<div class="permalink-wrapper"><h2 id="([^"]+)">(.+?)<\/h2><a href="#\1" class="heading-permalink" title=""><span class="sr-only">.*?<\/span><\/a><\/div>/s';

    private const string DATE_PATTERN = '/^\s*<p><em>(\d{4}-\d{2}-\d{2})<\/em><\/p>\s*/';

    /**
     * Pages whose `##` sections render as accordion items. The changelog is the library's own file and cannot carry
     * frontmatter, so the page is named here.
     */
    private const array ACCORDION_PAGES = ['docs/releases'];

    /**
     * Splits the page when it is one of the accordion pages, and hands every other page back whole. The table of
     * contents of a split page lists the sections only, the headings inside them are not worth a link each.
     *
     * @param list<TocItem> $toc
     *
     * @return array{
     *     intro: string,
     *     sections: list<array{id: string, title: string, date: string, body: string}>,
     *     toc: list<TocItem>,
     * }
     */
    public static function forPage(string $path, string $html, array $toc): array
    {
        if (!in_array($path, self::ACCORDION_PAGES, strict: true)) {
            return ['intro' => $html, 'sections' => [], 'toc' => $toc];
        }

        return [
            ...self::split($html),
            'toc' => array_values(array_filter($toc, static fn(TocItem $item): bool => $item->depth === 2)),
        ];
    }

    /**
     * @return array{
     *     intro: string,
     *     sections: list<array{id: string, title: string, date: string, body: string}>,
     * } The html before the first heading and the sections.
     */
    public static function split(string $html): array
    {
        // [intro, id, title, body, id, title, body, ...]
        $parts = preg_split(self::HEADING_PATTERN, $html, flags: PREG_SPLIT_DELIM_CAPTURE) ?: [$html];

        $intro = array_shift($parts);
        $sections = [];
        foreach (array_chunk($parts, length: 3) as [$id, $title, $body]) {
            $date = '';
            $matches = [];
            if (preg_match(self::DATE_PATTERN, $body, $matches) === 1) {
                $date = $matches[1];
                $body = substr($body, strlen($matches[0]));
            }

            $sections[] = ['id' => $id, 'title' => $title, 'date' => $date, 'body' => $body];
        }

        return ['intro' => $intro, 'sections' => $sections];
    }
}
