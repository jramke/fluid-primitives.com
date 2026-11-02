<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Controller;

use FluidPrimitives\Docs\CommonMark\PageHeadings;
use FluidPrimitives\Docs\Services\ComponentShortcodeResolver;
use FluidPrimitives\Docs\Services\NavigationBuilder;
use FluidPrimitives\Docs\Utility\DocsUtility;
use League\CommonMark\Parser\MarkdownParser;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

final class SearchIndexController extends ActionController
{
    private readonly VariableFrontend $cache;

    public function __construct(
        private readonly NavigationBuilder $navigationBuilder,
        CacheManager $cacheManager,
    ) {
        $cache = $cacheManager->getCache('docs_search_index');
        if (!$cache instanceof VariableFrontend) {
            throw new \RuntimeException(
                'Expected the "docs_search_index" cache to be configured as a VariableFrontend.',
                1757928000,
            );
        }
        $this->cache = $cache;
    }

    public function indexAction(): ResponseInterface
    {
        if (!$this->cache->has('index')) {
            $this->cache->set('index', $this->buildIndex());
        }

        /** @var string $json */
        $json = $this->cache->get('index');

        $response = $this->jsonResponse($json)->withStatus(200);
        throw new PropagateResponseException($response, 200);
    }

    private function buildIndex(): string
    {
        $baseDir = GeneralUtility::getFileAbsFileName('EXT:docs/Resources/Private/Content/');
        $navigation = $this->navigationBuilder->buildNavigation($baseDir, $baseDir . 'nav.yaml');

        $index = ['pages' => [], 'sections' => []];
        foreach ($navigation as $group) {
            foreach ($group['items'] as $item) {
                $slug = $item['slug'];
                $title = $item['title'];
                $groupTitle = (string)$group['title'];

                $filePath = $baseDir . ltrim($slug, characters: '/') . '.md';
                if (!is_file($filePath)) {
                    continue;
                }

                $content = file_get_contents($filePath);
                if ($content === false) {
                    continue;
                }

                // A demo is a Fluid render each and nearly all of the build time, and the words of a demo are no prose.
                // The text goes through the plain converter: highlighting and permalinks add nothing to it.
                $markdown = ComponentShortcodeResolver::strip(
                    $this->stripFrontmatter($content),
                    [
                        'ui:componentExample',
                    ],
                );
                $markdown = ComponentShortcodeResolver::resolve($markdown, $this->request);

                $index['pages'][] = [
                    'title' => $title,
                    'group' => $groupTitle,
                    'url' => $slug,
                    'content' => $this->htmlToPlainText(DocsUtility::simpleMarkdownToHtml($markdown)),
                ];
                array_push($index['sections'], ...$this->sections($markdown, $title, $slug));
            }
        }

        return json_encode($index, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    /**
     * The h2 and h3 of a page with the ids the page gives them: parsed, not rendered, by the converter of the page. The
     * h3 under "API Reference" are the parts and API tables of a component and get a group of their own.
     *
     * @return list<array{title: string, page: string, trail: list<string>, group: string, url: string}>
     */
    private function sections(string $markdown, string $pageTitle, string $url): array
    {
        $document = (new MarkdownParser(DocsUtility::getMarkdownConverter()->getEnvironment()))->parse($markdown);

        $sections = [];
        $parent = null;
        foreach (PageHeadings::collect($document) as $heading) {
            if ($heading->depth === 2) {
                $parent = $heading->label;
            }

            $isApi = $heading->depth === 3 && $parent === 'API Reference';
            $sections[] = [
                'title' => $heading->label,
                'page' => $pageTitle,
                'trail' => $heading->depth === 3 && $parent !== null && !$isApi
                    ? [$pageTitle, $parent, $heading->label]
                    : [$pageTitle, $heading->label],
                'group' => $isApi ? 'API Reference' : 'Sections',
                'url' => $url . '#' . $heading->value,
            ];
        }

        return $sections;
    }

    private function stripFrontmatter(string $content): string
    {
        $matches = [];
        if (preg_match('/^---\n(.*?)\n---\n/s', $content, $matches)) {
            return substr($content, strlen($matches[0]));
        }

        return $content;
    }

    private function htmlToPlainText(string $html): string
    {
        $text = (string)preg_replace('/<(script|style)\b[^>]*>.*?<\/\1>/is', replacement: ' ', subject: $html);
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5);
        $text = (string)preg_replace('/\s+/', replacement: ' ', subject: $text);

        return trim($text);
    }
}
