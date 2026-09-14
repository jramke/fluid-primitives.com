<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Routing\Aspect;

use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Gathers the set of paths considered valid for the docs routing aspect: a handful of hardcoded
 * entry points, every path referenced in nav.yaml, and every redirect source in redirects.yaml.
 */
final class ValidPathsCollector
{
    /**
     * @return array<string, true>
     */
    public function collect(): array
    {
        $paths = [
            '' => true,
            'the-pitch' => true,
        ];

        if (Environment::getContext()->isDevelopment()) {
            $paths['playground'] = true;
        }

        foreach ($this->extractPathsFromNav() as $path) {
            $paths[$path] = true;
        }

        foreach ($this->extractRedirectSources() as $source) {
            if (str_starts_with($source, 'http://') || str_starts_with($source, 'https://')) {
                continue;
            }

            $paths[$source] = true;
        }

        return $paths;
    }

    /**
     * @return list<string>
     */
    private function extractPathsFromNav(): array
    {
        $navFile = GeneralUtility::getFileAbsFileName('EXT:docs/Resources/Private/Content/nav.yaml');

        if (!file_exists($navFile)) {
            return [];
        }

        // Narrowed immediately below via is_array() - nav.yaml is hand-maintained, so its shape is
        // deliberately not trusted any further than "some array of some shape".
        // @mago-expect analysis:mixed-assignment
        try {
            $navData = Yaml::parseFile($navFile);
        } catch (\Exception) {
            return [];
        }

        if (!is_array($navData)) {
            return [];
        }

        $paths = [];

        // Narrowed immediately below via is_array()/is_string() - same reasoning as above.
        // @mago-expect analysis:mixed-assignment
        foreach ($navData as $group) {
            if (!is_array($group) || !is_array($group['items'] ?? null)) {
                continue;
            }

            // @mago-expect analysis:mixed-assignment
            foreach ($group['items'] as $item) {
                if (!is_string($item) || $item === '') {
                    continue;
                }

                $paths[] = $item;
            }
        }

        return $paths;
    }

    /**
     * @return list<string>
     */
    private function extractRedirectSources(): array
    {
        $redirectsFile = GeneralUtility::getFileAbsFileName('EXT:docs/Resources/Private/Content/redirects.yaml');

        if (!file_exists($redirectsFile)) {
            return [];
        }

        // Narrowed immediately below via is_array() - redirects.yaml is hand-maintained, so its
        // shape is deliberately not trusted any further than "some array of some shape".
        // @mago-expect analysis:mixed-assignment
        try {
            $redirects = Yaml::parseFile($redirectsFile);
        } catch (\Exception) {
            return [];
        }

        if (!is_array($redirects)) {
            return [];
        }

        return array_map(strval(...), array_keys($redirects));
    }
}
