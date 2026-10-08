<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Components\Contexts;

use FluidPrimitives\Docs\Services\MachineDocs;
use FluidPrimitives\Docs\Traits\IsMarkdownModeAwareTrait;
use FluidPrimitives\Docs\Utility\DocsUtility;
use Jramke\FluidPrimitives\Annotations\RequiredAtRuntimeArgumentAnnotation;
use Jramke\FluidPrimitives\Component\ComponentPrimitivesCollection;
use Jramke\FluidPrimitives\Contexts\AbstractComponentContext;
use Jramke\FluidPrimitives\Utility\ComponentNameUtility;
use Jramke\FluidPrimitives\Utility\Typed;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\ArgumentDefinition;

class ComponentPropsTableContext extends AbstractComponentContext
{
    use IsMarkdownModeAwareTrait;

    private const array HIDDEN_PROPS = ['spreadProps'];

    private ?MachineDocs $machineDocs = null;

    public function getPartsWithProps(): array
    {
        $primitivesCollection = GeneralUtility::makeInstance(ComponentPrimitivesCollection::class);
        $componentName = lcfirst(Typed::string($this->get('name')));

        /** @var list<array{0: string, 1?: string}> $parts */
        $parts = Typed::arrayOrNull($this->get('parts')) ?? [];

        return array_map(function (array $value) use ($primitivesCollection, $componentName) {
            $part = $value[0];
            $text = $value[1] ?? '';
            $viewHelperName = $part === '' ? $componentName : "{$componentName}.{$part}";
            $compDefinition = $primitivesCollection->getComponentDefinition($viewHelperName);
            $props = array_filter(
                $compDefinition->getArgumentDefinitions(),
                static fn($value) => !in_array($value, self::HIDDEN_PROPS, strict: true),
                ARRAY_FILTER_USE_KEY,
            );
            return [
                'name' => $compDefinition->getName(),
                'props' => $this->buildPropsInfo($props),
                'description' => DocsUtility::simpleMarkdownToHtml($text),
                'descriptionMarkdown' => $text,
                'dataAttributes' => $this->getMachineDocs()->dataAttributes($part),
            ];
        }, $parts);
    }

    public function getAccessibility(): array
    {
        return $this->getMachineDocs()->accessibility();
    }

    public function getMachineApi(): array
    {
        return $this->getMachineDocs()->api();
    }

    public function getMachineOptions(): array
    {
        return $this->getMachineDocs()->options();
    }

    public function getIsMachineSource(): bool
    {
        return $this->getMachineDocs()->isMachineSource();
    }

    private function getMachineDocs(): MachineDocs
    {
        return $this->machineDocs ??= MachineDocs::forPrimitive(
            ComponentNameUtility::camelCaseToLowerCaseDashed(Typed::string($this->get('name'))),
            $this->getIsMarkdownMode(),
        );
    }

    /**
     * @param array<string, ArgumentDefinition> $props
     */
    private function buildPropsInfo(array $props): array
    {
        $propsInfo = [];
        foreach ($props as $propDefinition) {
            $hasRequiredAtRuntimeAnnotation =
                count(array_filter(
                    $propDefinition->getAnnotations(),
                    static fn($annotation) => $annotation instanceof RequiredAtRuntimeArgumentAnnotation,
                )) > 0;

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
        return ComponentNameUtility::camelCaseToLowerCaseDashed(Typed::string($this->get('name')));
    }
}
