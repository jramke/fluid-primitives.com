<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Controller;

use FluidPrimitives\Docs\Services\NavigationBuilder;
use FluidPrimitives\Docs\Utility\DocsUtility;
use Jramke\FluidPrimitives\Utility\Typed;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

final class SearchIndexController extends ActionController
{
    public function __construct(
        private readonly NavigationBuilder $navigationBuilder,
    ) {}

    public function indexAction(): ResponseInterface
    {
        $baseDir = GeneralUtility::getFileAbsFileName('EXT:docs/Resources/Private/Content/');
        $navigation = $this->navigationBuilder->buildNavigation($baseDir, $baseDir . 'nav.yaml');

        $index = [];
        foreach ($navigation as $group) {
            $items = Typed::arrayOrNull($group['items'] ?? null);
            if ($items === null) {
                continue;
            }

            foreach ($items as $item) {
                $slug = (string)($item['slug'] ?? '');
                $title = (string)($item['title'] ?? '');
                $groupTitle = (string)($group['title'] ?? '');

                $filePath = $baseDir . ltrim((string)$slug, '/') . '.md';
                if (!is_file($filePath)) {
                    continue;
                }

                $content = file_get_contents($filePath);
                if ($content === false) {
                    continue;
                }

                $markdown = $this->stripFrontmatter($content);
                [$html] = DocsUtility::markdownToHtml($markdown, $this->request);

                $index[] = [
                    'title' => $title,
                    'group' => $groupTitle,
                    'url' => $slug,
                    'content' => $this->htmlToPlainText($html),
                ];
            }
        }

        $response = $this->jsonResponse(json_encode(
            $index,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
        ))->withStatus(200);
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
