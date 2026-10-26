<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Services;

use FluidPrimitives\Docs\Utility\DocFileMetadataExtractor;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Builds the llms.txt index (see https://llmstxt.org), served by LlmsTxtMiddleware: every nav.yaml doc,
 * grouped the same way as the sidebar, linked at its `.md` URL with a short description.
 */
class LlmsTxtBuilder
{
    /**
     * The index of the docs content of this extension, with every link below `$baseUrl`.
     */
    public function buildForSite(string $baseUrl): string
    {
        $baseDir = GeneralUtility::getFileAbsFileName('EXT:docs/Resources/Private/Content/');

        return $this->build($baseDir, $baseDir . 'nav.yaml', $baseUrl);
    }

    public function build(string $baseDir, string $navFile, string $baseUrl): string
    {
        if (!file_exists($navFile)) {
            throw new \Exception('navFile not found', 1757843323);
        }

        /** @var list<array{group?: string, items?: list<string>}> $navConfig */
        $navConfig = Yaml::parseFile($navFile);

        $lines = [
            '# Fluid Primitives',
            '',
            'Documentation for Fluid Primitives, a headless component library for TYPO3 Fluid templating. ' .
                'Components are server-rendered with PHP/Fluid and hydrated client-side with TypeScript using Zag.js state machines.',
        ];

        foreach ($navConfig as $section) {
            $group = $section['group'] ?? null;
            $items = $section['items'] ?? [];

            if (!is_string($group) || $items === []) {
                continue;
            }

            $itemLines = $this->buildItemLines($baseDir, $baseUrl, $items);
            if ($itemLines === []) {
                continue;
            }

            $lines[] = '';
            $lines[] = "## {$group}";
            $lines[] = '';
            array_push($lines, ...$itemLines);
        }

        return implode("\n", $lines) . "\n";
    }

    /**
     * @param list<string> $items
     * @return list<string>
     */
    private function buildItemLines(string $baseDir, string $baseUrl, array $items): array
    {
        $lines = [];

        foreach ($items as $slug) {
            $filePath = $baseDir . $slug . '.md';
            if (!is_file($filePath)) {
                continue;
            }

            $title = DocFileMetadataExtractor::extractTitle($filePath);
            $description = DocFileMetadataExtractor::extractDescription($filePath);
            $url = "{$baseUrl}/{$slug}.md";

            $lines[] = $description === '' ? "- [{$title}]({$url})" : "- [{$title}]({$url}): {$description}";
        }

        return $lines;
    }
}
