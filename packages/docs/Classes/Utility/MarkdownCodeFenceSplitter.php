<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Utility;

/**
 * Splits a Markdown document into alternating text/fenced-code-block segments, shared by the post-
 * processing passes in MarkdownWhitespaceNormalizer and MarkdownLinkRewriter - both need to leave fenced
 * code blocks (component source, not prose) untouched while transforming everything around them.
 */
class MarkdownCodeFenceSplitter
{
    /**
     * @return list<string> alternating [text, code, text, code, ...] segments - a code segment always
     * starts with the fence delimiter (```)
     */
    public static function split(string $markdown): array
    {
        return preg_split('/(```.*?```)/s', $markdown, flags: PREG_SPLIT_DELIM_CAPTURE) ?: [$markdown];
    }

    public static function isCodeSegment(string $segment): bool
    {
        return str_starts_with($segment, '```');
    }
}
