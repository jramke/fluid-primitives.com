<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Services;

use FluidPrimitives\Docs\Utility\DocsUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Core\Http\Response;

/**
 * Handles the `.md` request path's responses for DocsController - detecting markdown mode (stamped by
 * DocsMarkdownModeMiddleware).
 *
 * The 200 case (respondWithContentIfActive()) returns its Response instead of throwing: `pageMarkdown`
 * (see page.typoscript) renders `lib.docsPlugin` directly rather than through PAGEVIEW, so this response
 * body becomes DocsController's entire Extbase return value with nothing left to wrap it in an HTML
 * layout, and it can flow through TYPO3's normal request lifecycle - and page cache - instead of
 * bypassing it. Its Content-Type/Cache-Control headers are irrelevant either way and deliberately left
 * off: ExtbasePluginContentObject (core) only keeps the response *body* from a plugin dispatch that
 * doesn't throw, discarding any headers/status set on it - those have to be (and are) declared in
 * `pageMarkdown.config.additionalHeaders` instead.
 *
 * The 404 case (respondNotFoundIfActive()) still throws PropagateResponseException - an Extbase return
 * here would otherwise get wrapped in the page's full HTML layout the same way (see
 * DocsController::registrationAction() for the same pattern elsewhere), and a 404 isn't worth caching.
 *
 * Kept as a single collaborator, rather than inline `if` branches in the controller, so the
 * markdown-mode decision points don't count against DocsController's own complexity budget.
 */
final readonly class DocsMarkdownModeResponder
{
    public function __construct(
        private StreamFactoryInterface $streamFactory,
    ) {}

    public function isActive(ServerRequestInterface $request): bool
    {
        return $request->getAttribute('docsMarkdownMode') === true;
    }

    /**
     * @throws PropagateResponseException when markdown mode is active
     */
    public function respondNotFoundIfActive(ServerRequestInterface $request): void
    {
        if (!$this->isActive($request)) {
            return;
        }

        throw new PropagateResponseException(new Response($this->streamFactory->createStream('Not Found'), 404, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]), 404);
    }

    public function respondWithContentIfActive(string $markdown, ServerRequestInterface $request): ?ResponseInterface
    {
        if (!$this->isActive($request)) {
            return null;
        }

        $body = DocsUtility::renderMarkdownForLlm($markdown, $request);
        return new Response($this->streamFactory->createStream($body));
    }

    /**
     * An internal redirect target keeps markdown mode across the redirect; an external one (e.g. the
     * `github` shorthand in redirects.yaml) is never a doc page and can't take a `.md` suffix.
     */
    public function redirectTarget(string $target, ServerRequestInterface $request): string
    {
        return $this->isActive($request) && str_starts_with($target, '/') ? $target . '.md' : $target;
    }
}
