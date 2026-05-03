<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Components\Contexts;

use FluidPrimitives\Docs\Utility\DocsUtility;
use Jramke\FluidPrimitives\Annotations\RequiredAtRuntimeArgumentAnnotation;
use Jramke\FluidPrimitives\Component\ComponentPrimitivesCollection;
use Jramke\FluidPrimitives\Constants;
use Jramke\FluidPrimitives\Contexts\AbstractComponentContext;
use Jramke\FluidPrimitives\Utility\ComponentUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ComponentPropsTableContext extends AbstractComponentContext
{
    public function getPartsWithProps()
    {
        $primitivesCollection = GeneralUtility::makeInstance(ComponentPrimitivesCollection::class);
        $componentName = lcfirst((string)($this->get('name') ?? ''));
        $parts = $this->get('parts');

        $componentDefinitions = array_map(function ($value) use ($primitivesCollection, $componentName) {
            $part = $value[0];
            $text = $value[1] ?? '';
            $viewHelperName = $part === '' ? $componentName : "{$componentName}.{$part}";
            $compDefinition = $primitivesCollection->getComponentDefinition($viewHelperName);
            $props = array_filter(
                $compDefinition->getArgumentDefinitions(),
                static function ($value) {
                    return !in_array($value, Constants::GLOBAL_PROPS);
                },
                ARRAY_FILTER_USE_KEY,
            );
            return [
                'name' => $compDefinition->getName(),
                'props' => $this->buildPropsInfo($props),
                'description' => DocsUtility::simpleMarkdownToHtml($text),
            ];
        }, $parts);

        return $componentDefinitions;
    }

    private function buildPropsInfo(array $props): array
    {
        $propsInfo = [];
        foreach ($props as $propDefinition) {
            $hasRequiredAtRuntimeAnnotation =
                count(array_filter($propDefinition->getAnnotations(), static function ($annotation) {
                    return $annotation instanceof RequiredAtRuntimeArgumentAnnotation;
                })) > 0;

            $propInfo = [
                'name' => $propDefinition->getName(),
                'type' => DocsUtility::displayType($propDefinition->getType()),
                'description' => $propDefinition->getDescription(),
                'required' => $propDefinition->isRequired() || $hasRequiredAtRuntimeAnnotation ? 'Yes' : 'No',
                'default' => DocsUtility::displayValue($propDefinition->getDefaultValue()),
                'cases' => DocsUtility::getCasesStringFromType($propDefinition->getType()),
            ];
            $propsInfo[] = $propInfo;
        }
        return $propsInfo;
    }

    public function getNameLowerCaseDashed(): string
    {
        return ComponentUtility::camelCaseToLowerCaseDashed($this->get('name') ?? '');
    }
}
