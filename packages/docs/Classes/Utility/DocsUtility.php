<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Utility;

use FluidPrimitives\Docs\Phiki\PhikiCommonMarkExtension;
use FluidPrimitives\Docs\Services\ComponentShortcodeResolver;
use FluidPrimitives\Docs\Services\MarkdownLinkRewriter;
use FluidPrimitives\Docs\Services\MarkdownWhitespaceNormalizer;
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

// DocsUtility groups small, independent static formatting/markdown helpers shared across the docs
// site's Fluid context classes - splitting them into per-concern classes would just move the same
// call sites around without reducing complexity.
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
            return (
                (
                    $value === []
                        ? '[]'
                        : json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
                ) ?: '[]'
            );
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
        if (!enum_exists($type) || !is_subclass_of($type, \UnitEnum::class, allow_string: true)) {
            return '';
        }
        $cases = [];
        // is_subclass_of() above guarantees $type is a concrete enum, not the UnitEnum interface
        // itself - it never returns true for a type matching itself.
        // @mago-expect analysis:possibly-static-access-on-interface
        foreach ($type::cases() as $case) {
            $cases[] = $case->name;
        }

        return implode(' | ', $cases);
    }

    public static function displayType(string $type): string
    {
        return str_replace('Jramke\\FluidPrimitives\\', replace: '', subject: $type);
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
     * Resolves `{% component: ... %}` shortcodes the same way markdownToHtml() does, but leaves the
     * result as plain Markdown instead of converting it to HTML - used for the `.md` request path
     * (DocsMarkdownModeMiddleware), where each shortcode's Fluid template emits its own Markdown-native
     * branch (fenced code blocks, tables, plain links) instead of interactive HTML.
     */
    public static function renderMarkdownForLlm(string $markdown, ServerRequestInterface $request): string
    {
        $resolved = ComponentShortcodeResolver::resolve($markdown, $request);
        $normalized = MarkdownWhitespaceNormalizer::normalize($resolved);
        return MarkdownLinkRewriter::rewriteInternalLinks($normalized);
    }

    /**
     * @return array{string, string} First item is the content html, second item is the toc html
     */
    public static function markdownToHtml(string $markdown, ServerRequestInterface $request): array
    {
        $processedMarkdown = ComponentShortcodeResolver::resolve($markdown, $request);
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
        $content = self::makePermalinksAccessible($content);

        if ($toc instanceof Node) {
            $toc = $renderer->renderNodes([$toc]);
            $toc = str_replace(
                '<ul class="table-of-contents">',
                replace: '<ul class="table-of-contents"><li><a href="#">(Top)</a></li>',
                subject: $toc,
            );
        }

        return [$content, (string)$toc];
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
                    'aria_hidden' => false,
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

    private static function wrapCodeBlocks(string $html): string
    {
        // Match <pre> tags that do NOT have class="not-code-block"
        $pattern = '/(<pre\b(?![^>]*\bclass\s*=\s*["\'][^"\']*\bnot-code-block\b[^"\']*["\']).*?<\/pre>)/is';
        $replacement = '<div class="code-block"><div>$1</div></div>';

        return preg_replace($pattern, $replacement, $html) ?? $html;
    }

    private static function wrapTables(string $html): string
    {
        // Match <table> tags that do NOT have class="not-prose"
        $pattern = '/(<table\b(?![^>]*\bclass\s*=\s*["\'][^"\']*\bnot-prose\b[^"\']*["\']).*?<\/table>)/is';
        $replacement = '<div class="table-wrapper">$1</div>';

        return preg_replace($pattern, $replacement, $html) ?? $html;
    }

    private static function makePermalinksAccessible(string $html): string
    {
        $pattern = '/<h([2-6]) id="([^"]+)">(.+?)<a href="#\2" class="heading-permalink" title=""><\/a><\/h\1>/i';
        $replacement = '<div class="permalink-wrapper"><h$1 id="$2">$3</h$1><a href="#$2" class="heading-permalink" title=""><span class="sr-only">Permalink to heading "$3"</span></a></div>';

        return preg_replace($pattern, $replacement, $html) ?? $html;
    }
}
