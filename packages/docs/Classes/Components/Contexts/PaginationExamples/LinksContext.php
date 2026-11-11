<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Components\Contexts\PaginationExamples;

final class LinksContext extends AbstractEventListContext
{
    protected function getPageArgument(): string
    {
        return 'eventPage';
    }
}
