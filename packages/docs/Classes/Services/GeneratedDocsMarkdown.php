<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Services;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

/**
 * Converts the JSDoc text of the generated docs data: a literal `<` of a type name is escaped instead of read
 * as HTML, and with `$inline` a single paragraph comes back without its `<p>`, so it fits a table cell.
 */
final class GeneratedDocsMarkdown
{
    private static ?MarkdownConverter $converter = null;

    public static function toHtml(string $markdown, bool $inline = true): string
    {
        if (!self::$converter instanceof MarkdownConverter) {
            $environment = new Environment(['html_input' => 'escape', 'allow_unsafe_links' => false]);
            $environment->addExtension(new CommonMarkCoreExtension());
            self::$converter = new MarkdownConverter($environment);
        }

        $html = trim(self::$converter->convert($markdown)->getContent());
        $isSingleParagraph =
            str_starts_with($html, '<p>') && str_ends_with($html, '</p>') && substr_count($html, needle: '<p>') === 1;

        // ComponentShortcodeResolver drops whitespace between two tags, which would glue `**Returns** `T``
        // together: an entity survives that.
        $html =
            preg_replace(
                '/(<\/(?:strong|em|code|a)>) (?=<(?:strong|em|code|a)\b)/',
                replacement: '$1&#32;',
                subject: $html,
            ) ?? $html;

        return $inline && $isSingleParagraph ? substr($html, offset: 3, length: -4) : $html;
    }
}
