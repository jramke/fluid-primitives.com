<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Services;

use Symfony\Component\Yaml\Yaml;

class NavigationBuilder
{
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
            $title = $this->extractTitle($file->getPathname());

            $docs[$slug] = [
                'slug' => '/' . $slug,
                'title' => $title,
            ];
        }

        return $docs;
    }

    private function extractTitle(string $filePath): string
    {
        $lines = file($filePath) ?: [];
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '# ')) {
                return trim(ltrim($line, characters: '# '));
            }
        }
        return basename($filePath, suffix: '.md');
    }
}
