<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Traits;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Shared by docs Context classes whose template renders a different, Markdown-native branch for the
 * `.md` request path (see DocsMarkdownModeMiddleware). Requires the using class to have a
 * `getRequest(): ServerRequestInterface` method (provided by AbstractComponentContext).
 *
 * Named `getIsMarkdownMode()`, not `isMarkdownMode()` - AbstractComponentContext::offsetGet() only
 * dispatches `{context.isMarkdownMode}` to a `get{Ucfirst($offset)}` method, it has no `is*`/`has*`
 * boolean-getter convention.
 */
trait IsMarkdownModeAwareTrait
{
    abstract public function getRequest(): ServerRequestInterface;

    public function getIsMarkdownMode(): bool
    {
        return $this->getRequest()->getAttribute('docsMarkdownMode') === true;
    }
}
