<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Utility;

/**
 * Pulls a title and a short description out of a docs Markdown file, for the sidebar navigation and
 * llms.txt - both need "what is this page called / about" without rendering the whole page.
 */
class DocFileMetadataExtractor
{
    private const int DESCRIPTION_MAX_LENGTH = 200;

    public static function extractTitle(string $filePath): string
    {
        $lines = file($filePath) ?: [];
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '# ')) {
                return trim(ltrim($line, characters: '# '));
            }
        }
        return basename($filePath, suffix: '.md');
    }

    /**
     * The docs content consistently follows its `# Title` with a one-paragraph summary (plain prose or
     * a bold one-liner, see e.g. docs/components/textarea.md, docs/viewhelpers/attributes.md) before any
     * `{% component: ... %}` shortcode or further headings - that paragraph is what gets extracted here.
     */
    public static function extractDescription(string $filePath): string
    {
        $content = file_get_contents($filePath) ?: '';

        $afterHeading = preg_split('/^#\s+.+$/m', $content, limit: 2)[1] ?? '';

        $stripped = preg_replace('/<!--[\s\S]*?-->/', replacement: '', subject: $afterHeading) ?? $afterHeading;
        $stripped = preg_replace('/\{%[\s\S]*?%\}/', replacement: '', subject: $stripped) ?? $stripped;

        $paragraphs = preg_split('/\n\s*\n/', trim($stripped)) ?: [];

        return self::toPlainText(trim($paragraphs[0] ?? ''));
    }

    private static function toPlainText(string $markdown): string
    {
        $text = preg_replace('/\[([^\]]+)\]\([^)]+\)/', replacement: '$1', subject: $markdown) ?? $markdown;
        $text = preg_replace('/[*_`]+/', replacement: '', subject: $text) ?? $text;
        $text = trim(preg_replace('/\s+/', replacement: ' ', subject: $text) ?? $text);

        if (mb_strlen($text) <= self::DESCRIPTION_MAX_LENGTH) {
            return $text;
        }

        $truncated = mb_substr($text, start: 0, length: self::DESCRIPTION_MAX_LENGTH);
        $lastSpace = mb_strrpos($truncated, needle: ' ');
        if ($lastSpace !== false) {
            $truncated = mb_substr($truncated, start: 0, length: $lastSpace);
        }

        return rtrim($truncated, characters: '.,;:') . '...';
    }
}
