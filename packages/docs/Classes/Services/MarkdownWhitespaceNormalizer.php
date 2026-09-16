<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Services;

use FluidPrimitives\Docs\Utility\MarkdownCodeFenceSplitter;

/**
 * Cleans up whitespace that leaks from the Fluid templates behind `{% component: ... %}` shortcodes
 * into the resolved `.md` response (see ComponentShortcodeResolver) - Fluid preserves a template's own
 * indentation verbatim, and reformatting those templates (Prettier, hand-edits) keeps changing that
 * indentation, so the output can't rely on the template source staying flat. Runs as a single pass over
 * the whole resolved document (DocsUtility::renderMarkdownForLlm()), leaving fenced code blocks
 * untouched - their indentation is real source code, not template noise.
 */
class MarkdownWhitespaceNormalizer
{
    public static function normalize(string $markdown): string
    {
        $segments = MarkdownCodeFenceSplitter::split($markdown);

        foreach ($segments as $index => $segment) {
            $segments[$index] = MarkdownCodeFenceSplitter::isCodeSegment($segment)
                ? self::normalizeCodeSegment($segment)
                : self::normalizeProse($segment);
        }

        return implode('', $segments);
    }

    /**
     * A fenced block's content is real source code and keeps its own internal indentation verbatim -
     * except the line right after the opening fence, which is where template noise actually shows up:
     * whatever precedes `{tab.templateRaw -> f:format.raw()}` in the template (see
     * ComponentExample.fluid.html) gets prepended only to the *first* line of that multi-line
     * interpolated value, same as any PHP string interpolation. Every other content line is copied
     * verbatim from a real source file and already carries its own correct, meaningful indentation.
     */
    private static function normalizeCodeSegment(string $segment): string
    {
        $lines = explode("\n", $segment);
        $lastIndex = count($lines) - 1;

        if ($lastIndex < 1) {
            return $segment;
        }

        $lines[0] = ltrim($lines[0]);
        $lines[$lastIndex] = ltrim($lines[$lastIndex]);

        if ($lastIndex > 1) {
            $lines[1] = ltrim($lines[1]);
        }

        return implode("\n", $lines);
    }

    /**
     * Strips leading/trailing whitespace from every line, and joins consecutive plain-prose lines
     * (Fluid's own line-wrapping around a tag boundary, not an intentional line break) into one -
     * table rows, headings, blockquotes and list items are structural and always kept on their own line.
     */
    private static function normalizeProse(string $segment): string
    {
        $lines = [];
        $buffer = [];

        foreach (explode("\n", $segment) as $line) {
            $trimmed = trim($line);

            if ($trimmed === '') {
                [$lines, $buffer] = self::flushBuffer($lines, $buffer);
                $lines[] = '';
                continue;
            }

            if (self::isStructuralLine($trimmed)) {
                [$lines, $buffer] = self::flushBuffer($lines, $buffer);
                $lines[] = $trimmed;
                continue;
            }

            $buffer[] = $trimmed;
        }
        [$lines] = self::flushBuffer($lines, $buffer);

        $result = implode("\n", $lines);

        // Nested `<f:for>`/`<f:if>` blocks each contribute their own blank-line separators - collapse
        // any resulting run down to a single blank line.
        return preg_replace('/\n{3,}/', replacement: "\n\n", subject: $result) ?? $result;
    }

    /**
     * Joins any buffered prose lines into a single output line and clears the buffer - a plain
     * parameter-in/parameter-out helper (rather than a by-ref closure capturing `$lines`/`$buffer`
     * from normalizeProse()) so the flow between the two states stays something static analysis can
     * actually follow.
     *
     * @param list<string> $lines
     * @param list<string> $buffer
     * @return array{0: list<string>, 1: list<string>}
     */
    private static function flushBuffer(array $lines, array $buffer): array
    {
        if ($buffer === []) {
            return [$lines, $buffer];
        }

        $lines[] = implode(' ', $buffer);
        return [$lines, []];
    }

    private static function isStructuralLine(string $trimmedLine): bool
    {
        return (
            str_starts_with($trimmedLine, '|') ||
            str_starts_with($trimmedLine, '#') ||
            str_starts_with($trimmedLine, '>') ||
            (bool)preg_match('/^(-|\*|\d+\.)\s/', $trimmedLine)
        );
    }
}
