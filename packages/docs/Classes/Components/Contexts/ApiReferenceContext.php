<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Components\Contexts;

use FluidPrimitives\Docs\Phiki\RemoveLangClassTransformer;
use FluidPrimitives\Docs\Services\ApiReferenceBlockBuilder;
use FluidPrimitives\Docs\Services\GeneratedDocsMarkdown;
use FluidPrimitives\Docs\Traits\IsMarkdownModeAwareTrait;
use FluidPrimitives\Docs\Utility\GeneratedDocsMetadata;
use Jramke\FluidPrimitives\Contexts\AbstractComponentContext;
use Jramke\FluidPrimitives\Utility\Typed;
use Phiki\Grammar\Grammar;
use Phiki\Phiki;
use Phiki\Theme\Theme;

/**
 * The blocks of one client export for the `ui:apiReference` template: Markdown as it is for the `.md`
 * output, HTML (converted Markdown, highlighted code) for the page.
 */
class ApiReferenceContext extends AbstractComponentContext
{
    use IsMarkdownModeAwareTrait;

    /**
     * @return list<array<string, mixed>>
     */
    public function getBlocks(): array
    {
        $blocks = ApiReferenceBlockBuilder::build(GeneratedDocsMetadata::forSymbol(Typed::string($this->get(
            'symbol',
        ))));

        return $this->getIsMarkdownMode() ? $blocks : array_map($this->withHtml(...), $blocks);
    }

    /**
     * @param array<string, mixed> $block
     * @return array<string, mixed>
     */
    private function withHtml(array $block): array
    {
        return match ($block['type']) {
            'text' => [
                ...$block,
                'html' => GeneratedDocsMarkdown::toHtml(Typed::string($block['markdown']), inline: false),
            ],
            'code' => [
                ...$block,
                'html' => $this->highlight(Typed::string($block['code']), Typed::string($block['language'])),
            ],
            'table' => [
                ...$block,
                'rows' => array_map(
                    static fn(array $row): array => [
                        ...$row,
                        'description' => GeneratedDocsMarkdown::toHtml(Typed::string($row['description'])),
                    ],
                    Typed::arrayOrNull($block['rows']) ?? [],
                ),
            ],
            default => $block,
        };
    }

    private function highlight(string $code, string $language): string
    {
        return (new Phiki())
            ->codeToHtml($code, Grammar::tryFrom($language) ?? Grammar::Txt, Theme::GithubLight)
            ->transformer(new RemoveLangClassTransformer())
            ->toString();
    }
}
