<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Components\Contexts;

use FluidPrimitives\Docs\Phiki\RemoveLangClassTransformer;
use Jramke\FluidPrimitives\Contexts\AbstractComponentContext;
use Phiki\Grammar\Grammar;
use Phiki\Phiki;
use Phiki\Theme\Theme;
use Phiki\Transformers\Decorations\PreDecoration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Component\ComponentDefinitionProviderInterface;
use TYPO3Fluid\Fluid\Core\Component\ComponentTemplateResolverInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperResolverDelegateInterface;

class ComponentExampleContext extends AbstractComponentContext
{
    private ViewHelperResolverDelegateInterface & ComponentTemplateResolverInterface & ComponentDefinitionProviderInterface $viewHelperResolverDelegate;

    public function getHtml(): string
    {
        $componentRenderer = $this->getComponentResolver()->getComponentRenderer();
        $html = $componentRenderer->renderComponent($this->get('componentName'), ['class' => 'not-prose'], [], $this->getRenderingContext());

        return $html;
    }

    public function getTabs(): array
    {
        $mainTemplateString = $this->getMainComponentTemplateString();
        $tabs = [
            [
                'label' => explode('.', $this->get('componentName') ?? 'Example')[0] . '.html',
                'templateHighlighted' => $this->highlightTemplateString($mainTemplateString, 'html'),
                'templateRaw' => $mainTemplateString,
            ]
        ];

        foreach ($this->get('additionalFiles') ?? [] as $label => $path) {
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
        $templateName = $this->getComponentResolver()->resolveTemplateName($this->get('componentName'));
        $templateString = $this->getComponentResolver()->getTemplatePaths()->getTemplateSource('Default', $templateName);

        return $templateString;
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
        ];

        $grammar = $languages[$language] ?? Grammar::Txt;

        return (new Phiki)
            ->codeToHtml($templateString, $grammar, Theme::GithubLight)
            ->decoration(PreDecoration::make()->class('not-code-block'))
            ->transformer(new RemoveLangClassTransformer)
            ->toString();
    }
}
