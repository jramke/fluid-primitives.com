<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Routing\Aspect;

use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Routing\Aspect\StaticMappableAspectInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ValidatedPathMapper implements StaticMappableAspectInterface
{
    private const int MAX_PATH_LENGTH = 200;
    private const string ALLOWED_PATTERN = '/^[a-z0-9\/_-]*$/i';
    private const string CACHE_IDENTIFIER = 'docs_validated_paths';
    private const int CACHE_LIFETIME = 0; // Unlimited

    private array $validPaths = [];
    private static ?FrontendInterface $cache = null;

    public function __construct(array $settings = [])
    {
        $this->initializeCache();
        $this->loadValidPaths();
    }

    public function generate(string $value): ?string
    {
        // homepage case
        if ($value === '') {
            return '';
        }

        if (!$this->isValidPath($value)) {
            return null;
        }

        return $value;
    }

    public function resolve(string $value): ?string
    {
        $path = urldecode($value);

        // homepage case
        if ($path === '') {
            return '';
        }

        if (!$this->validatePathSecurity($path)) {
            return null;
        }

        if (!$this->isValidPath($path)) {
            return null;
        }

        return $path;
    }

    private function validatePathSecurity(string $path): bool
    {
        if (strlen($path) > self::MAX_PATH_LENGTH) {
            return false;
        }

        if (!preg_match(self::ALLOWED_PATTERN, $path)) {
            return false;
        }

        if (str_contains($path, '..') || str_contains($path, './') || str_contains($path, '\\')) {
            return false;
        }

        if (str_contains($path, '//')) {
            return false;
        }
        return !str_starts_with($path, '/') && !str_ends_with($path, '/');
    }

    private function isValidPath(string $path): bool
    {
        return isset($this->validPaths[$path]);
    }

    private function loadValidPaths(): void
    {
        if (self::$cache instanceof FrontendInterface) {
            $cached = self::$cache->get(self::CACHE_IDENTIFIER);
            if (is_array($cached) && $cached !== []) {
                $this->validPaths = $cached;
                return;
            }
        }

        $this->validPaths = $this->extractValidPaths();

        if (self::$cache instanceof FrontendInterface) {
            self::$cache->set(self::CACHE_IDENTIFIER, $this->validPaths, [], self::CACHE_LIFETIME);
        }
    }

    private function extractValidPaths(): array
    {
        $paths = [];

        $paths[''] = true;

        if (Environment::getContext()->isDevelopment()) {
            $paths['playground'] = true;
        }

        $navPaths = $this->extractPathsFromNav();
        foreach ($navPaths as $path) {
            $paths[$path] = true;
        }

        $redirectSources = $this->extractRedirectSources();
        foreach ($redirectSources as $source) {
            if (!(!str_starts_with((string)$source, 'http://') && !str_starts_with((string)$source, 'https://'))) {
                continue;
            }

            $paths[$source] = true;
        }

        return $paths;
    }

    private function extractPathsFromNav(): array
    {
        $navFile = GeneralUtility::getFileAbsFileName('EXT:docs/Resources/Private/Content/nav.yaml');

        if (!file_exists($navFile)) {
            return [];
        }

        try {
            $navData = Yaml::parseFile($navFile);
        } catch (\Exception) {
            return [];
        }

        if (!is_array($navData)) {
            return [];
        }

        $paths = [];

        foreach ($navData as $group) {
            if (!isset($group['items']) || !is_array($group['items'])) {
                continue;
            }

            foreach ($group['items'] as $item) {
                if (!(is_string($item) && $item !== '')) {
                    continue;
                }

                $paths[] = $item;
            }
        }

        return $paths;
    }

    private function extractRedirectSources(): array
    {
        $redirectsFile = GeneralUtility::getFileAbsFileName('EXT:docs/Resources/Private/Content/redirects.yaml');

        if (!file_exists($redirectsFile)) {
            return [];
        }

        try {
            $redirects = Yaml::parseFile($redirectsFile);
        } catch (\Exception) {
            return [];
        }

        if (!is_array($redirects)) {
            return [];
        }

        return array_keys($redirects);
    }

    private function initializeCache(): void
    {
        if (self::$cache instanceof FrontendInterface) {
            return;
        }

        try {
            $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
            self::$cache = $cacheManager->getCache('pages');
        } catch (\Exception) {
            self::$cache = null;
        }
    }

    public static function clearCache(): void
    {
        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
        $cache = $cacheManager->getCache('pages');
        $cache->remove(self::CACHE_IDENTIFIER);
    }
}
