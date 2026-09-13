<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Controller;

use FluidPrimitives\Docs\Services\NavigationBuilder;
use FluidPrimitives\Docs\Utility\DocsUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

final class SearchIndexController extends ActionController
{
    public function __construct(
        private readonly NavigationBuilder $navigationBuilder,
    ) {}

    /**
     * Builds the client-side search index: one entry per doc page (title, group, url and its
     * fully rendered content as plain text, so `{% component: "..." %}` placeholders - e.g. the
     * API props tables - are included the same way they appear on the real page). Consumed
     * once by the cmd+k search on the client and indexed there, rather than searched server-side.
     */
    public function indexAction(): ResponseInterface
    {
        $baseDir = GeneralUtility::getFileAbsFileName('EXT:docs/Resources/Private/Content/');
        $navigation = $this->navigationBuilder->buildNavigation($baseDir, $baseDir . 'nav.yaml');

        $index = [];
        foreach ($navigation as $group) {
            foreach ($group['items'] as $item) {
                $filePath = $baseDir . ltrim((string)$item['slug'], '/') . '.md';
                if (!is_file($filePath)) {
                    continue;
                }

                $markdown = $this->stripFrontmatter(file_get_contents($filePath));
                [$html] = DocsUtility::markdownToHtml($markdown, $this->request);

                $index[] = [
                    'title' => $item['title'],
                    'group' => $group['title'],
                    'url' => $item['slug'],
                    'content' => $this->htmlToPlainText($html),
                ];
            }
        }

        $response = $this->jsonResponse(
            json_encode($index, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        )->withStatus(200);
        throw new PropagateResponseException($response, 200);
    }

    private function stripFrontmatter(string $content): string
    {
        if (preg_match('/^---\n(.*?)\n---\n/s', $content, $matches)) {
            return substr($content, strlen($matches[0]));
        }

        return $content;
    }

    private function htmlToPlainText(string $html): string
    {
        $text = (string)preg_replace('/<(script|style)\b[^>]*>.*?<\/\1>/is', ' ', $html);
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5);
        $text = (string)preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }
}
