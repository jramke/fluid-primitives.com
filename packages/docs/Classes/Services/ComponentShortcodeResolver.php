<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Services;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextFactory;
use TYPO3Fluid\Fluid\Core\Component\ComponentDefinitionProviderInterface;
use TYPO3Fluid\Fluid\Core\Component\ComponentTemplateResolverInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Resolves `{% component: "ui:xyz", arguments: {...} %}` shortcodes embedded in docs Markdown content.
 * Split out of DocsUtility (which otherwise stays a grab-bag of small formatting helpers) because
 * resolving a shortcode - into HTML for a normal page render, or into the component's own Markdown-native
 * branch when the request is in markdown mode (see DocsMarkdownModeMiddleware) - is a distinct concern
 * with real internal branching of its own.
 */
class ComponentShortcodeResolver
{
    private const string SHORTCODE_PATTERN = '/\{%\s*component:\s*"([^"]+)"(?:,\s*arguments:\s*(\{.*?\}))?\s*%\}/s';

    public static function resolve(string $markdown, ServerRequestInterface $request): string
    {
        return (
            preg_replace_callback(
                self::SHORTCODE_PATTERN,
                static fn(array $matches) => self::resolveShortcode($matches, $request),
                $markdown,
            ) ?? $markdown
        );
    }

    /**
     * @param array<array-key, string> $matches
     */
    private static function resolveShortcode(array $matches, ServerRequestInterface $request): string
    {
        $fullViewHelperName = $matches[1];

        /** @var array<string, mixed> $arguments */
        $arguments = array_key_exists(2, $matches) ? json_decode($matches[2], associative: true) ?? [] : [];

        try {
            $renderingContext = GeneralUtility::makeInstance(RenderingContextFactory::class)->create(request: $request);

            [$namespace, $viewHelperName] = explode(':', $fullViewHelperName);
            $viewHelperResolverDelegate = $renderingContext->getViewHelperResolver()->getResponsibleDelegate(
                $namespace,
                $viewHelperName,
            );

            if (
                !$viewHelperResolverDelegate instanceof ComponentDefinitionProviderInterface ||
                !$viewHelperResolverDelegate instanceof ComponentTemplateResolverInterface
            ) {
                return (
                    '<div class="fluid-template-error">Error: Unknown component "' .
                    htmlspecialchars($viewHelperName) .
                    '"</div>'
                );
            }

            $isMarkdownMode = $request->getAttribute('docsMarkdownMode') === true;
            $html = self::renderComponent(
                $viewHelperResolverDelegate,
                $fullViewHelperName,
                $arguments,
                $renderingContext,
                $isMarkdownMode,
            );

            // In markdown mode the component's own template already emitted plain Markdown (fenced
            // code, tables, links) - cleanHtmlForMarkdown()'s whitespace collapsing is an HTML-block
            // concern and would corrupt that, so only trim it.
            return $isMarkdownMode ? trim($html) : self::cleanHtmlForMarkdown($html);
        } catch (\Exception $e) {
            return '<div class="fluid-template-error">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }

    /**
     * @param array<string, mixed> $arguments
     */
    private static function renderComponent(
        ComponentDefinitionProviderInterface&ComponentTemplateResolverInterface $viewHelperResolverDelegate,
        string $fullViewHelperName,
        array $arguments,
        RenderingContextInterface $renderingContext,
        bool $isMarkdownMode,
    ): string {
        [, $viewHelperName] = explode(':', $fullViewHelperName);
        $componentRenderer = $viewHelperResolverDelegate->getComponentRenderer();

        if ($fullViewHelperName === 'ui:componentExample') {
            return $componentRenderer->renderComponent($viewHelperName, [...$arguments], [], $renderingContext);
        }

        $html = $componentRenderer->renderComponent(
            $viewHelperName,
            [...$arguments, 'class' => 'not-prose'],
            [],
            $renderingContext,
        );

        return $isMarkdownMode ? $html : '<div class="prose-component">' . $html . '</div>';
    }

    private static function cleanHtmlForMarkdown(string $html): string
    {
        // Extract <pre> blocks so we don't accidentally clean them up
        $preBlocks = [];
        $html = preg_replace_callback(
            '/<pre\b[^>]*>[\s\S]*?<\/pre>/i',
            static function ($matches) use (&$preBlocks) {
                $key = '###PRE_BLOCK_' . count($preBlocks) . '###';
                $preBlocks[$key] = $matches[0];
                return $key;
            },
            $html,
        );

        // Remove HTML comments
        $html = preg_replace('/<!--[\s\S]*?-->/', replacement: '', subject: (string)$html);
        // Collapse whitespace between tags
        $html = preg_replace('/>\s+</', replacement: '><', subject: (string)$html);
        // Collapse excessive whitespace inside tags/attributes
        $html = preg_replace('/\s{2,}/', replacement: ' ', subject: (string)$html);
        // Trim leading/trailing whitespace
        $html = trim((string)$html);

        // Restore <pre> blocks
        return strtr($html, $preBlocks);
    }
}
