<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Phiki;

use Phiki\Phast\ClassList;
use Phiki\Phast\Element;
use Phiki\Transformers\AbstractTransformer;

// This removes the language-* classes due to some conflicts with 1Password extension (Prism.js)
// @see https://github.com/phikiphp/phiki/issues/131
class RemoveLangClassTransformer extends AbstractTransformer
{
    #[\Override]
    public function pre(Element $pre): Element
    {
        // Narrowed immediately below via instanceof ClassList - Properties::get() itself is
        // generically typed as string|Stringable by the vendor class.
        // @mago-expect analysis:mixed-assignment
        $classes = $pre->properties->get('class');
        if (!$classes instanceof ClassList) {
            return $pre;
        }

        /** @var list<string> $classNames */
        $classNames = $classes->all();

        foreach ($classNames as $class) {
            if (!str_starts_with($class, 'language-')) {
                continue;
            }

            $classes->remove($class);
        }

        return $pre;
    }
}
