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
        $link =
            '<a href="' .
            htmlspecialchars($path) .
            '.md" class="view-as-markdown text-sm font-normal text-muted-foreground hover:text-foreground no-underline inline-flex items-center gap-1.5 shrink-0">' .
            '<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide size-4">' .
            '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>' .
            '<path d="M14 2v4a2 2 0 0 0 2 2h4"></path>' .
            '</svg>' .
            'View as Markdown' .
            '</a>';

        // Inside the heading rather than beside it, so it stays a direct `.prose > h1` child and keeps
        // Tailwind Typography's heading spacing instead of falling under sibling-margin rules meant for
        // separate prose elements.
        return (
            preg_replace(
                '/<h1>(.*?)<\/h1>/s',
                '<h1 class="flex flex-wrap items-center justify-between gap-x-4 gap-y-1"><span>$1</span>' .
                $link .
                '</h1>',
                $html,
                limit: 1,
            ) ?? $html
        );
    }
}
