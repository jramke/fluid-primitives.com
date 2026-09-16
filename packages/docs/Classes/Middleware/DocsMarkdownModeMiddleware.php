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
 */
final readonly class DocsMarkdownModeMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();

        if (!str_ends_with($path, '.md') || str_starts_with($path, '/registry/')) {
            return $handler->handle($request);
        }

        $request = $request->withUri($request->getUri()->withPath(substr($path, offset: 0, length: -3)))->withAttribute(
            'docsMarkdownMode',
            true,
        );

        return $handler->handle($request);
    }
}
