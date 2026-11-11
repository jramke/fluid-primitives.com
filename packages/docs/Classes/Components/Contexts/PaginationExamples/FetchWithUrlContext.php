<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Components\Contexts\PaginationExamples;

final class FetchWithUrlContext extends AbstractEventListContext
{
    protected function getPageArgument(): string
    {
        return 'archivePage';
    }

    protected function getSection(): string
    {
        return 'event-archive';
    }
}
