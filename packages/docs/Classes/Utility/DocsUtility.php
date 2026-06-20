<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Utility;

use FluidPrimitives\Docs\Phiki\PhikiCommonMarkExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TableOfContents\Node\TableOfContents;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Node\Node;
use League\CommonMark\Node\Query;
use League\CommonMark\Renderer\HtmlRenderer;
use Phiki\Theme\Theme;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextFactory;
use TYPO3Fluid\Fluid\Core\Component\ComponentDefinitionProviderInterface;
use TYPO3Fluid\Fluid\Core\Component\ComponentTemplateResolverInterface;

class DocsUtility
{
    private static ?MarkdownConverter $converter = null;
    private static ?MarkdownConverter $simpleConverter = null;

    public static function displayValue(mixed $value): string
    {
        if ($value === null) {
            return '-';
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_array($value)) {
            return $value === []
                ? '[]'
                : json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
        if (is_string($value)) {
            return "'{$value}'";
        }
        if ($value instanceof \BackedEnum || $value instanceof \UnitEnum) {
            return $value->name;
        }
        return (string)$value;
    }

    public static function getCasesStringFromType(string $type): string
    {
        if (!enum_exists($type)) {
            return '';
        }
        $cases = [];
        foreach ($type::cases() as $case) {
            $cases[] = $case->name;
        }

        return implode(' | ', $cases);
    }

    public static function displayType(string $type): string
    {
        return str_replace('Jramke\\FluidPrimitives\\', '', $type);
    }

    public static function simpleMarkdownToHtml(string $markdown): string
    {
        $converter = self::getSimpleMarkdownConverter();
        return $converter->convert($markdown)->getContent();
    }

    public static function getSimpleMarkdownConverter(): MarkdownConverter
    {
        if (!self::$simpleConverter instanceof MarkdownConverter) {
            $environment = new Environment([
                'external_link' => [
                    'internal_hosts' => $_SERVER['HTTP_HOST'],
                    'open_in_new_window' => true,
                ],
            ]);
            $environment->addExtension(new CommonMarkCoreExtension())->addExtension(new ExternalLinkExtension());

            self::$simpleConverter = new MarkdownConverter($environment);
        }

        return self::$simpleConverter;
    }

    /**
     * @return array{string, string} First item is the content html, second item is the toc html
     */
    public static function markdownToHtml(string $markdown, ServerRequestInterface $request): array
    {
        $processedMarkdown = DocsUtility::processFluidTemplatesInMarkdown($markdown, $request);
        $converter = DocsUtility::getMarkdownConverter();
        $converted = $converter->convert($processedMarkdown);

        $document = $converted->getDocument();

        $toc = (new Query())
            ->where(Query::type(TableOfContents::class))
            ->findOne($document);

        if ($toc instanceof Node) {
            $toc->detach();
        }

        $renderer = new HtmlRenderer($converter->getEnvironment());
        $content = $renderer->renderDocument($document);

        $content = self::wrapCodeBlocks($content->getContent());
        $content = self::wrapTables($content);

        if ($toc instanceof Node) {
            $toc = $renderer->renderNodes([$toc]);
            $toc = str_replace(
                '<ul class="table-of-contents">',
                '<ul class="table-of-contents"><li><a href="#">(Top)</a></li>',
                $toc,
            );
        }

        return [(string)$content, (string)$toc];
    }

    public static function getMarkdownConverter(): MarkdownConverter
    {
        if (!self::$converter instanceof MarkdownConverter) {
            $environment = new Environment([
                'heading_permalink' => [
                    'min_heading_level' => 2,
                    'max_heading_level' => 3,
                    'apply_id_to_heading' => true,
                    'title' => '',
                    'symbol' => '',
                    'insert' => 'after',
                ],
                'external_link' => [
                    'internal_hosts' => $_SERVER['HTTP_HOST'],
                    'open_in_new_window' => true,
                ],
                'table_of_contents' => [
                    'html_class' => 'table-of-contents',
                    'position' => 'top',
                    'style' => 'bullet',
                    'min_heading_level' => 2,
                    'max_heading_level' => 3,
                    'normalize' => 'relative',
                    'placeholder' => null,
                ],
            ]);

            $environment
                ->addExtension(new CommonMarkCoreExtension())
                ->addExtension(new PhikiCommonMarkExtension(Theme::GithubLight))
                ->addExtension(new HeadingPermalinkExtension())
                ->addExtension(new TableOfContentsExtension())
                ->addExtension(new ExternalLinkExtension())
                ->addExtension(new TableExtension());

            self::$converter = new MarkdownConverter($environment);
        }

        return self::$converter;
    }

    public static function processFluidTemplatesInMarkdown(string $markdown, ServerRequestInterface $request): string
    {
        return preg_replace_callback(
            '/\{%\s*component:\s*"([^"]+)"(?:,\s*arguments:\s*(\{.*?\}))?\s*%\}/s',
            static function ($matches) use ($request) {
                $fullViewHelperName = $matches[1];
                $arguments = isset($matches[2]) ? json_decode($matches[2], true) ?? [] : [];

                try {
                    $renderingContext = GeneralUtility::makeInstance(RenderingContextFactory::class)->create(
                        request: $request,
                    );

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

                    $isCodeExample = $fullViewHelperName === 'ui:componentExample';

                    $componentRenderer = $viewHelperResolverDelegate->getComponentRenderer();

                    if ($isCodeExample) {
                        $html = $componentRenderer->renderComponent(
                            $viewHelperName,
                            [
                                ...$arguments,
                            ],
                            [],
                            $renderingContext,
                        );
                    } else {
                        $html = $componentRenderer->renderComponent(
                            $viewHelperName,
                            [...$arguments, 'class' => 'not-prose'],
                            [],
                            $renderingContext,
                        );
                        $html = '<div class="prose-component">' . $html . '</div>';
                    }

                    return self::cleanHtmlForMarkdown($html);
                } catch (\Exception $e) {
                    return '<div class="fluid-template-error">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
            },
            $markdown,
        );
    }

    private static function wrapCodeBlocks(string $html): string
    {
        // Match <pre> tags that do NOT have class="not-code-block"
        $pattern = '/(<pre\b(?![^>]*\bclass\s*=\s*["\'][^"\']*\bnot-code-block\b[^"\']*["\']).*?<\/pre>)/is';
        $replacement = '<div class="code-block"><div>$1</div></div>';

        return preg_replace($pattern, $replacement, $html);
    }

    private static function wrapTables(string $html): string
    {
        // Match <table> tags that do NOT have class="not-prose"
        $pattern = '/(<table\b(?![^>]*\bclass\s*=\s*["\'][^"\']*\bnot-prose\b[^"\']*["\']).*?<\/table>)/is';
        $replacement = '<div class="table-wrapper">$1</div>';

        return preg_replace($pattern, $replacement, $html);
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
        $html = preg_replace('/<!--[\s\S]*?-->/', '', (string)$html);
        // Collapse whitespace between tags
        $html = preg_replace('/>\s+</', '><', (string)$html);
        // Collapse excessive whitespace inside tags/attributes
        $html = preg_replace('/\s{2,}/', ' ', (string)$html);
        // Trim leading/trailing whitespace
        $html = trim((string)$html);

        // Restore <pre> blocks
        return strtr($html, $preBlocks);
    }
}
