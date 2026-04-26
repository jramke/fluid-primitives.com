<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Utility;

use FluidPrimitives\Docs\Phiki\PhikiCommonMarkExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\MarkdownConverter;
use Phiki\Theme\Theme;

class DocsUtility
{
    private static ?MarkdownConverter $converter = null;
    private static ?MarkdownConverter $simpleConverter = null;

    public static function displayValue($value): string
    {
        if ($value === null) {
            return '-';
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_array($value)) {
            return empty($value)
                ? '[]'
                : json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
        if (is_string($value)) {
            return "'{$value}'";
        }
        return (string)$value;
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
        if (self::$simpleConverter === null) {
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

    public static function getMarkdownConverter(): MarkdownConverter
    {
        if (self::$converter === null) {
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
}
