<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Registry;

use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class ComponentRegistry
{
    private array $components = [];
    private VariableFrontend $cache;

    public function __construct(CacheManager $cacheManager)
    {
        $this->cache = $cacheManager->getCache('fluid_primitives_registry');
        $this->load();
    }

    public function has(string $component): bool
    {
        return isset($this->components[$component]);
    }

    public function get(string $component): ComponentRegistryDefinition
    {
        if (!$this->has($component)) {
            throw new \InvalidArgumentException('Unknown component', 1766956244);
        }

        return $this->components[$component];
    }

    public function list(): array
    {
        return array_values($this->components);
    }

    private function load(): void
    {
        if ($this->cache->has('components')) {
            $this->components = $this->cache->get('components');
            return;
        }

        $registryFile = GeneralUtility::getFileAbsFileName(
            'EXT:docs/Resources/Private/Registry/registry.yaml'
        );

        if (!is_file($registryFile)) {
            return;
        }

        $data = Yaml::parseFile($registryFile);
        if (!is_array($data)) {
            return;
        }

        $baseDir = dirname($registryFile) . '/';

        foreach ($data as $key => $config) {
            if (!is_array($config) || empty($config['files'])) {
                continue;
            }

            $componentDir = $baseDir . ($config['name'] ?? '') . '/';
            if (empty($componentDir) || !is_dir($componentDir)) {
                continue;
            }

            $files = [];
            foreach ($config['files'] as $file) {
                if (!is_string($file)) {
                    continue;
                }

                if (!preg_match('/^[A-Za-z0-9._-]+$/', $file)) {
                    continue;
                }

                if (is_file($componentDir . $file)) {
                    $files[] = $file;
                }
            }

            if ($files === []) {
                continue;
            }

            $this->components[$key] = new ComponentRegistryDefinition(
                key: $key,
                name: $config['name'],
                basePath: $componentDir,
                files: $files,
                meta: $config,
            );
        }

        $this->cache->set('components', $this->components);
    }
}
