<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Services;

use FluidPrimitives\Docs\Utility\MarkdownCodeFenceSplitter;

/**
 * Appends `.md` to internal doc links in a Markdown document, so an agent following a link from one
 * `.md` page (see DocsMarkdownModeMiddleware) lands on another `.md` page instead of the interactive
 * HTML one. Runs as a single pass over the whole resolved document (DocsUtility::renderMarkdownForLlm())
 * rather than per-template, so it catches both prose links authored directly in the content and ones
 * emitted by a resolved `{% component: ... %}` shortcode (e.g. InstallationSection's "Read more" link).
 */
class MarkdownLinkRewriter
{
    public static function rewriteInternalLinks(string $markdown): string
    {
        $segments = MarkdownCodeFenceSplitter::split($markdown);

        foreach ($segments as $index => $segment) {
            // Leave fenced code blocks untouched - they're component source, not prose, and could
            // coincidentally contain a `](` sequence.
            if (MarkdownCodeFenceSplitter::isCodeSegment($segment)) {
                continue;
            }

            $segments[$index] = self::rewriteLinksInSegment($segment);
        }

        return implode('', $segments);
    }

    private static function rewriteLinksInSegment(string $segment): string
    {
        return (
            preg_replace_callback(
                '/\]\(([^)\s]+)([^)]*)\)/',
                static fn(array $matches) => '](' . self::rewriteUrl($matches[1]) . $matches[2] . ')',
                $segment,
            ) ?? $segment
        );
    }

    private static function rewriteUrl(string $url): string
    {
        if ($url === '' || $url === '/' || !str_starts_with($url, '/') || str_starts_with($url, '//')) {
            return $url;
        }

        $splitAt = strcspn($url, characters: '#?');
        $path = substr($url, offset: 0, length: $splitAt);
        $suffix = substr($url, offset: $splitAt);

        if ($path === '' || str_ends_with($path, '.md') || str_contains(basename($path), '.')) {
            return $url;
        }

        return $path . '.md' . $suffix;
    }
}
