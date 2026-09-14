<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Components\Contexts;

use FluidPrimitives\Docs\Utility\DocsUtility;
use FluidPrimitives\Docs\Utility\ZagDocsMetadata;
use Jramke\FluidPrimitives\Annotations\RequiredAtRuntimeArgumentAnnotation;
use Jramke\FluidPrimitives\Component\ComponentPrimitivesCollection;
use Jramke\FluidPrimitives\Contexts\AbstractComponentContext;
use Jramke\FluidPrimitives\Utility\ComponentNameUtility;
use Jramke\FluidPrimitives\Utility\Typed;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\ArgumentDefinition;

class ComponentPropsTableContext extends AbstractComponentContext
{
    /**
     * Internal/plumbing props that should never show up in a part's Arguments table,
     * regardless of whether the part actually declares them - unlike Constants::GLOBAL_PROPS
     * (which also feeds Storybook's control exclusion), this list intentionally omits
     * `class`, `asChild` and `attributes` so they surface wherever a part actually has them.
     */
    private const array HIDDEN_PROPS = ['ids', 'rootId', 'controlled', 'spreadProps'];

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
                'dataAttributes' => $this->getZagDataAttributesForPart($part),
            ];
        }, $parts);
    }

    public function getZagAccessibility(): array
    {
        if ($this->get('skipZag') === true) {
            return [];
        }

        $name = Typed::string($this->get('name'));
        if ($name === '') {
            return [];
        }

        $metadata = ZagDocsMetadata::forPrimitive(ComponentNameUtility::camelCaseToLowerCaseDashed($name));
        $accessibility = Typed::arrayOrNull($metadata['accessibility'] ?? null) ?? [];

        return Typed::arrayOrNull($accessibility['keyboard'] ?? null) ?? [];
    }

    public function getZagApi(): array
    {
        if ($this->get('skipZag') === true) {
            return [];
        }

        $name = Typed::string($this->get('name'));
        if ($name === '') {
            return [];
        }

        $metadata = ZagDocsMetadata::forPrimitive(ComponentNameUtility::camelCaseToLowerCaseDashed($name));

        return Typed::arrayOrNull($metadata['api'] ?? null) ?? [];
    }

    private function getZagDataAttributesForPart(string $partName): array
    {
        if ($this->get('skipZag') === true) {
            return [];
        }

        $name = Typed::string($this->get('name'));
        if ($name === '') {
            return [];
        }

        $primitive = ZagDocsMetadata::forPrimitive(ComponentNameUtility::camelCaseToLowerCaseDashed($name));
        $attributes = Typed::arrayOrNull($primitive['dataAttributes'] ?? null) ?? [];
        if ($attributes === []) {
            return [];
        }

        $normalizedPartName = $this->normalizeZagPartName($partName);
        if ($normalizedPartName === '') {
            return [];
        }

        // Narrowed immediately below via is_array() - attributes values come from decoded JSON
        // metadata, so their shape isn't statically known any further than "array of mixed".
        // @mago-expect analysis:mixed-assignment
        $data = $attributes[$normalizedPartName] ?? [];
        if (!is_array($data)) {
            return [];
        }

        return $data;
    }

    private function normalizeZagPartName(string $partName): string
    {
        if ($partName === '') {
            return '';
        }

        $value = preg_replace('/[^a-zA-Z0-9]+/', replacement: ' ', subject: $partName);
        $value = ucwords((string)$value);
        return str_replace(' ', replace: '', subject: $value);
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
