<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Services;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\CMS\Core\Http\RequestFactory;

/**
 * The version select entries: the latest release of every major on Packagist. The highest major is
 * this site, every older one is its own deployment at `v<major>.fluid-primitives.com`.
 */
final class VersionsProvider
{
    private const string PACKAGE = 'jramke/fluid-primitives';
    private const int LIFETIME = 3600;
    private const int FAILURE_LIFETIME = 300;

    private readonly VariableFrontend $cache;

    public function __construct(
        private readonly RequestFactory $requestFactory,
        CacheManager $cacheManager,
    ) {
        $cache = $cacheManager->getCache('docs_versions');
        if (!$cache instanceof VariableFrontend) {
            throw new \RuntimeException(
                'Expected the "docs_versions" cache to be configured as a VariableFrontend.',
                1760284800,
            );
        }
        $this->cache = $cache;
    }

    /**
     * @return list<array{label: string, href: string, current?: true}> Empty when Packagist is unreachable, which hides the select.
     */
    public function getVersions(): array
    {
        if ($this->cache->has('versions')) {
            /** @var list<array{label: string, href: string, current?: true}> $versions */
            $versions = $this->cache->get('versions');
            return $versions;
        }

        $versions = $this->build($this->fetchReleases());
        $this->cache->set('versions', $versions, [], $versions === [] ? self::FAILURE_LIFETIME : self::LIFETIME);

        return $versions;
    }

    /**
     * @return list<string>
     */
    private function fetchReleases(): array
    {
        try {
            $response = $this->requestFactory->request(
                'https://repo.packagist.org/p2/' . self::PACKAGE . '.json',
                'GET',
                ['timeout' => 3],
            );
            // Narrowed by is_array() below and typed by the @var: the decoded JSON is untyped by nature.
            // @mago-expect analysis:mixed-assignment
            $data = json_decode((string)$response->getBody(), true, flags: JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return [];
        }
        if (!is_array($data)) {
            return [];
        }

        /** @var array{packages?: array<string, list<array{version?: string}>>} $data */
        $releases = [];
        foreach ($data['packages'][self::PACKAGE] ?? [] as $release) {
            if (isset($release['version'])) {
                $releases[] = ltrim($release['version'], 'v');
            }
        }

        return $releases;
    }

    /**
     * @param list<string> $releases
     * @return list<array{label: string, href: string, current?: true}>
     */
    private function build(array $releases): array
    {
        usort($releases, static fn(string $a, string $b): int => version_compare($b, $a));

        $byMajor = [];
        foreach ($releases as $release) {
            $byMajor[(int)$release][] = $release;
        }
        krsort($byMajor);

        $versions = [];
        foreach ($byMajor as $major => $majorReleases) {
            // A stable release wins over a newer prerelease of the same major.
            $stable = array_values(array_filter($majorReleases, static fn(string $r): bool => !str_contains($r, '-')));
            $latest = $stable[0] ?? $majorReleases[0];
            $isCurrent = $versions === [];

            $versions[] =
                [
                    'label' => "v{$latest}",
                    'href' => $isCurrent ? '/docs' : "https://v{$major}.fluid-primitives.com/docs",
                ] + ($isCurrent ? ['current' => true] : []);
        }

        return $versions;
    }
}
