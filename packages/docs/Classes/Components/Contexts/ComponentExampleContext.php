<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Components\Contexts;

use FluidPrimitives\Docs\Phiki\RemoveLangClassTransformer;
use Jramke\FluidPrimitives\Contexts\AbstractComponentContext;
use Jramke\FluidPrimitives\Utility\Typed;
use Phiki\Grammar\Grammar;
use Phiki\Phiki;
use Phiki\Theme\Theme;
use Phiki\Transformers\Decorations\PreDecoration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ComponentExampleContext extends AbstractComponentContext
{
    private ?string $html = null;

    // Memoized: `{context.html}` in the Fluid template resolves through `offsetExists()` before
    // `offsetGet()` (see AbstractComponentContext), so this getter runs twice per template access.
    // renderComponent() has hydration side effects (ComponentHydrationCollector registers the
    // rendered component for client-side mounting) - rendering the example component twice would
    // register it twice, causing double client-side hydration of the same DOM.
    public function getHtml(): string
    {
        if ($this->html === null) {
            $componentRenderer = $this->getComponentResolver()->getComponentRenderer();
            $this->html = $componentRenderer->renderComponent(
                Typed::string($this->get('componentName')),
                ['class' => 'not-prose'],
                [],
                $this->getRenderingContext(),
            );
        }

        return $this->html;
    }

    public function getTabs(): array
    {
        $componentName = Typed::string($this->get('componentName'), 'Example');
        $mainTemplateString = $this->getMainComponentTemplateString();
        $tabs = [
            [
                'label' => explode('.', $componentName)[0] . '.html',
                'templateHighlighted' => $this->highlightTemplateString($mainTemplateString, 'html'),
                'templateRaw' => $mainTemplateString,
            ],
        ];

        if ($this->get('withEntryFile')) {
            $entryFileTemplateString = $this->getEntryFileTemplateString();
            if ($entryFileTemplateString !== '' && $entryFileTemplateString !== '0') {
                $tabs[] = [
                    'label' => explode('.', $componentName)[0] . '.ts',
                    'templateHighlighted' => $this->highlightTemplateString($entryFileTemplateString, 'ts'),
                    'templateRaw' => $entryFileTemplateString,
                ];
            }
        }

        // Narrowed immediately below via Typed::string() - additionalFiles values are Fluid context
        // data, so their element type isn't statically known any further than "array of mixed".
        // @mago-expect analysis:mixed-assignment
        foreach (Typed::arrayOrNull($this->get('additionalFiles')) ?? [] as $label => $path) {
            $path = Typed::string($path);
            $templateString = $this->getTemplateStringByPath($path);
            $language = pathinfo($path, PATHINFO_EXTENSION);
            $tabs[] = [
                'label' => $label,
                'templateHighlighted' => $this->highlightTemplateString($templateString, $language),
                'templateRaw' => $templateString,
            ];
        }

        return $tabs;
    }

    private function getMainComponentTemplateString(): string
    {
        $templateName = $this->getComponentResolver()->resolveTemplateName(Typed::string($this->get('componentName')));
        return $this->getComponentResolver()->getTemplatePaths()->getTemplateSource('Default', $templateName);
    }

    private function getEntryFileTemplateString(): string
    {
        $componentBaseName = explode('.', Typed::string($this->get('componentName')))[0];
        foreach ($this->getComponentResolver()->getTemplatePaths()->getTemplateRootPaths() as $rootPath) {
            $entryFilePath = $rootPath . $componentBaseName . '/' . $componentBaseName . '.entry.ts';
            $templateString = $this->getTemplateStringByPath($entryFilePath);
            if ($templateString !== '' && $templateString !== '0') {
                return $templateString;
            }
        }
        return '';
    }

    private function getTemplateStringByPath(string $filePath): string
    {
        $absPath = GeneralUtility::getFileAbsFileName($filePath);
        if (!file_exists($absPath)) {
            return '';
        }
        return file_get_contents($absPath) ?: '';
    }

    private function highlightTemplateString(string $templateString, string $language): string
    {
        $languages = [
            'html' => Grammar::Html,
            'js' => Grammar::Javascript,
            'ts' => Grammar::Typescript,
            'css' => Grammar::Css,
            'php' => Grammar::Php,
        ];

        $grammar = $languages[$language] ?? Grammar::Txt;

        return (new Phiki())
            ->codeToHtml($templateString, $grammar, Theme::GithubLight)
            ->decoration(PreDecoration::make()->class('not-code-block'))
            ->transformer(new RemoveLangClassTransformer())
            ->toString();
    }
}
