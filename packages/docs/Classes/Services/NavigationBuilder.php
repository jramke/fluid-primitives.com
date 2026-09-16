<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Services;

use FluidPrimitives\Docs\Utility\DocFileMetadataExtractor;
use Symfony\Component\Yaml\Yaml;

class NavigationBuilder
{
    private const string LLMS_TXT_GROUP = 'Overview';

    public function buildNavigation(string $baseDir, string $navFile): array
    {
        $allDocs = $this->scanDocs($baseDir);

        if (!file_exists($navFile)) {
            throw new \Exception('navFile not found', 1757843323);
        }

        /** @var list<array{group?: string, items: list<string>}> $navConfig */
        $navConfig = Yaml::parseFile($navFile);
        $navigation = [];

        foreach ($navConfig as $section) {
            $group = [
                'title' => $section['group'] ?? null,
                'items' => [],
            ];

            foreach ($section['items'] as $slug) {
                if (!array_key_exists($slug, $allDocs)) {
                    continue;
                }

                $group['items'][] = $allDocs[$slug];
                unset($allDocs[$slug]);
            }

            // llms.txt isn't a scanned content file, just a synthetic sidebar link into the
            // middleware-served /llms.txt - added here rather than nav.yaml so it doesn't have to
            // pretend to be a real doc for scanDocs()/ValidPathsCollector's sake.
            if ($group['title'] === self::LLMS_TXT_GROUP) {
                $group['items'][] = ['slug' => '/llms.txt', 'title' => 'llms.txt'];
            }

            $navigation[] = $group;
        }

        return $navigation;
    }

    private function scanDocs(string $baseDir): array
    {
        $docs = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($baseDir));

        foreach ($iterator as $file) {
            /** @var \SplFileInfo $file */
            if ($file->getExtension() !== 'md') {
                continue;
            }

            $relPath = str_replace(search: $baseDir, replace: '', subject: $file->getPathname());
            $slug = str_replace(search: '.md', replace: '', subject: $relPath);
            $title = DocFileMetadataExtractor::extractTitle($file->getPathname());

            $docs[$slug] = [
                'slug' => '/' . $slug,
                'title' => $title,
            ];
        }

        return $docs;
    }
}
