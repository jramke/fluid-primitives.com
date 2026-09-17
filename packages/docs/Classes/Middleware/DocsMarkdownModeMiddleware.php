<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Lets any markdown-backed docs page be fetched as plain Markdown by appending `.md` to its URL (e.g.
 * `/docs/components/textarea.md`). Runs before routing because `ValidatedPathMapper`'s security pattern
 * rejects a literal dot, so the suffix has to be stripped before the page-resolver ever sees the path.
 * `DocsController::showAction()` reads the `docsMarkdownMode` attribute stamped here to switch its response.
 *
 * Also stamps a distinct `type` (see MARKDOWN_TYPE_NUM) into the request's query params. Without it,
 * `/foo` and `/foo.md` resolve to the exact same page id/type/route, so TYPO3's page cache (keyed on
 * those, not on the `docsMarkdownMode` attribute - see createPageCacheIdentifier() in core's
 * PrepareTypoScriptFrontendRendering) can't tell them apart: whichever variant renders first wins that
 * cache slot for both URLs. Giving `.md` requests their own typeNum (registered as `pageMarkdown` in
 * page.typoscript) gives them their own cache identity instead. `withQueryParams()` alone is enough -
 * PageRouter::matchRequest() (core) reads the page type from `$request->getQueryParams()['type']`, never
 * from the URI's own query string - and it's internal only, never added to a redirect, so the browser's
 * address bar and the public URL stay exactly `/foo.md`.
 */
final readonly class DocsMarkdownModeMiddleware implements MiddlewareInterface
{
    private const int MARKDOWN_TYPE_NUM = 9576;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();

        if (!str_ends_with($path, '.md') || str_starts_with($path, '/registry/')) {
            return $handler->handle($request);
        }

        $request = $request
            ->withUri($request->getUri()->withPath(substr($path, offset: 0, length: -3)))
            ->withQueryParams([...$request->getQueryParams(), 'type' => (string)self::MARKDOWN_TYPE_NUM])
            ->withAttribute('docsMarkdownMode', true);

        return $handler->handle($request);
    }
}
