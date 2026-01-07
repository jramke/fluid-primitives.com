<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class ArrayChunkViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('subject', 'array', 'The array to chunk', false, null);
        $this->registerArgument('size', 'int', 'Size of each chunk', true);
        $this->registerArgument('preserveKeys', 'bool', 'Whether to preserve array keys', false, false);
        $this->registerArgument('as', 'string', 'Variable name for the result if used as tag-based ViewHelper');
    }

    public function getContentArgumentName(): string
    {
        return 'subject';
    }

    public function render(): mixed
    {
        $subject = $this->renderChildren();
        $size = (int)$this->arguments['size'];
        $preserveKeys = (bool)$this->arguments['preserveKeys'];
        $as = $this->arguments['as'];

        if (!is_array($subject) && !$subject instanceof \Traversable) {
            return $subject;
        }

        if ($subject instanceof \Traversable) {
            $subject = iterator_to_array($subject);
        }

        if ($size < 1) {
            return $subject;
        }

        $chunks = array_chunk($subject, $size, $preserveKeys);

        if ($as !== null) {
            $variableProvider = $this->renderingContext->getVariableProvider();
            $variableProvider->add($as, $chunks);
            $output = $this->renderChildren();
            $variableProvider->remove($as);
            return $output;
        }

        return $chunks;
    }
}
