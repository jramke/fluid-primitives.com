<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Phiki;

use Phiki\Phast\Element;
use Phiki\Transformers\AbstractTransformer;

// This removes the language-* classes due to some conflicts with 1Password extension (Prism.js) 
// @see https://github.com/phikiphp/phiki/issues/131
class RemoveLangClassTransformer extends AbstractTransformer
{
    public function pre(Element $pre): Element
    {
        $classes = $pre->properties->get('class');
        if (!$classes) return $pre;

        foreach ($classes->all() as $class) {
            if (str_starts_with($class, 'language-')) {
                $classes->remove($class);
            }
        }

        return $pre;
    }
}
