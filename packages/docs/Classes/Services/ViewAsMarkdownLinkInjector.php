<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Services;

/**
 * Splices a "View as Markdown" link into a rendered doc page's `<h1>`, linking to that same page's
 * `.md` URL (see DocsMarkdownModeMiddleware). Only used for the normal HTML page render -
 * SearchIndexController's own DocsUtility::markdownToHtml() consumer doesn't want this, which is why
 * it isn't folded into that method.
 */
class ViewAsMarkdownLinkInjector
{
    public static function inject(string $html, string $path): string
    {
        $svg = <<<SVG
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M14.846 12.9233H1.154a1.153 1.153 0 0 1-.44136-.0878 1.152 1.152 0 0 1-.37416-.25 1.153 1.153 0 0 1-.25002-.3741 1.154 1.154 0 0 1-.08779-.4414V4.22999A1.15335 1.15335 0 0 1 1.154 3.07666h13.692c.1515 0 .3014.02983.4414.08779a1.1535 1.1535 0 0 1 .7119 1.06554v7.53871c.0001.1515-.0296.3015-.0876.4415-.0579.14-.1428.2673-.2499.3744a1.153 1.153 0 0 1-.3743.2502c-.14.058-.29.0885-.4415.0885m-11-2.308V7.61533l1.53867 1.92333 1.538-1.92333v2.99997h1.53867V5.38533H6.92267l-1.538 1.92333L3.846 5.38533H2.30734v5.23137zm10.308-2.61531h-1.5387V5.38466h-1.538v2.61533H9.53867L11.846 10.6927z" fill="currentColor"></path></svg>
        SVG;

        $link =
            '<a href="' .
            htmlspecialchars($path) .
            '.md"' .
            'data-view-as-markdown-link class="text-sm font-normal text-muted-foreground hover:text-foreground no-underline inline-flex items-center gap-1.5 shrink-0 mt-2 mb-2">' .
            $svg .
            'View as Markdown' .
            '</a>';

        if (str_contains($html, 'data-reference-buttons>')) {
            return str_replace('data-reference-buttons>', 'data-reference-buttons>' . $link, $html);
        }

        return str_replace('</h1>', '</h1>' . $link, $html);
    }
}
