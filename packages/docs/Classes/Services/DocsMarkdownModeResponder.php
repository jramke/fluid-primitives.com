<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Services;

use FluidPrimitives\Docs\Utility\DocsUtility;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Core\Http\Response;

/**
 * Handles the `.md` request path's responses for DocsController - detecting markdown mode (stamped by
 * DocsMarkdownModeMiddleware) and, when active, short-circuiting via PropagateResponseException before
 * the controller reaches its normal HTML view rendering (an Extbase return here would otherwise get
 * wrapped in the page's full HTML layout - see DocsController::registrationAction() for the same
 * pattern). Kept as a single collaborator, rather than inline `if` branches in the controller, so the
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

    /**
     * @throws PropagateResponseException when markdown mode is active
     */
    public function respondWithContentIfActive(string $markdown, ServerRequestInterface $request): void
    {
        if (!$this->isActive($request)) {
            return;
        }

        $body = DocsUtility::renderMarkdownForLlm($markdown, $request);
        throw new PropagateResponseException(new Response($this->streamFactory->createStream($body), 200, [
            'Content-Type' => 'text/markdown; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]), 200);
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
