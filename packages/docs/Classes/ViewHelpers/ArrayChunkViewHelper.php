<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\ViewHelpers;

use Jramke\FluidPrimitives\Utility\Typed;
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
        // Narrowed immediately below via is_array()/instanceof Traversable - render output is
        // genuinely arbitrary child content.
        // @mago-expect analysis:mixed-assignment
        $subject = $this->renderChildren();
        $size = Typed::int($this->arguments['size']);
        $preserveKeys = Typed::bool($this->arguments['preserveKeys']);
        $as = Typed::stringOrNull($this->arguments['as']);

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
            $renderingContext = $this->renderingContext ?? throw new \RuntimeException(
                'ArrayChunk ViewHelper is missing its rendering context.',
                1_758_849_303,
            );
            $variableProvider = $renderingContext->getVariableProvider();
            $variableProvider->add($as, $chunks);
            // render() itself returns mixed - child content is genuinely arbitrary.
            // @mago-expect analysis:mixed-assignment
            $output = $this->renderChildren();
            $variableProvider->remove($as);
            return $output;
        }

        return $chunks;
    }
}
