<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Components\Contexts;

use FluidPrimitives\Docs\Traits\IsMarkdownModeAwareTrait;
use Jramke\FluidPrimitives\Contexts\AbstractComponentContext;
use Jramke\FluidPrimitives\Utility\Typed;

class InstallationSectionContext extends AbstractComponentContext
{
    use IsMarkdownModeAwareTrait;

    /**
     * The registry folders of the components, as given: one name or a list of them.
     *
     * @return list<string>
     */
    public function getNames(): array
    {
        $names = Typed::arrayOrNull($this->get('name')) ?? [$this->get('name')];

        return array_values(array_map(Typed::string(...), $names));
    }

    public function getCommand(): string
    {
        return 'typo3 ui:add ' . implode(' ', array_map(lcfirst(...), $this->getNames()));
    }
}
