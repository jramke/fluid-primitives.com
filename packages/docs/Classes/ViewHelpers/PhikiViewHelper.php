<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\ViewHelpers;

use FluidPrimitives\Docs\Phiki\RemoveLangClassTransformer;
use Phiki\Grammar\Grammar;
use Phiki\Phiki;
use Phiki\Theme\Theme;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class PhikiViewHelper extends AbstractViewHelper
{
    protected $escapeChildren = false;
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('grammar', 'string', 'The grammar to use', true);
    }

    public function render(): string
    {
        $grammar = Grammar::tryFrom($this->arguments['grammar']) ?? Grammar::Txt;

        $html = (new Phiki())
            ->codeToHtml(trim($this->renderChildren()), $grammar, Theme::GithubLight)
            ->transformer(new RemoveLangClassTransformer())
            ->toString();

        return $html;
    }
}
