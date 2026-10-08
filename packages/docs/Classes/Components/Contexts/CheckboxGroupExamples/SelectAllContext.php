<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Components\Contexts\CheckboxGroupExamples;

use Jramke\FluidPrimitives\Attributes\ExposeToClient;
use Jramke\FluidPrimitives\Contexts\AbstractComponentContext;

/**
 * The demo data for the "select all" example - fixed/hardcoded, not a real configurable prop, so
 * it lives here rather than round-tripping through a `<ui:prop>` just to be readable both from the
 * template (`{context.items}`, via {@see AbstractComponentContext::offsetGet()}'s `getX()`
 * convention) and from the client (via `#[ExposeToClient]`) off the same single source.
 */
final class SelectAllContext extends AbstractComponentContext
{
    #[ExposeToClient]
    /**
     * @return list<array{value: string, text: string}>
     */
    public function getItems(): array
    {
        return [
            ['value' => 'dashboard', 'text' => 'View Dashboard'],
            ['value' => 'reports', 'text' => 'Access Reports'],
            ['value' => 'settings', 'text' => 'Modify Settings'],
        ];
    }
}
