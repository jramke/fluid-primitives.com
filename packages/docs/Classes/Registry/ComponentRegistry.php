<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Registry;

use Jramke\FluidPrimitives\Utility\Typed;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class ComponentRegistry
{
    /** @var array<string, ComponentRegistryDefinition> */
    private array $components = [];
    private readonly VariableFrontend $cache;

    public function __construct(CacheManager $cacheManager)
    {
        $cache = $cacheManager->getCache('fluid_primitives_registry');
        if (!$cache instanceof VariableFrontend) {
            throw new \RuntimeException(
                'Expected the "fluid_primitives_registry" cache to be configured as a VariableFrontend.',
                1758849302,
            );
        }
        $this->cache = $cache;
        $this->load();
    }

    public function has(string $component): bool
    {
        return array_key_exists($component, $this->components);
    }

    public function get(string $component): ComponentRegistryDefinition
    {
        if (!$this->has($component)) {
            throw new \InvalidArgumentException('Unknown component', 1766956244);
        }

        return $this->components[$component];
    }

    /**
     * @return list<ComponentRegistryDefinition>
     */
    public function list(): array
    {
        return array_values($this->components);
    }

    private function load(): void
    {
        if ($this->cache->has('components')) {
            /** @var array<string, ComponentRegistryDefinition> $components */
            $components = $this->cache->get('components');
            $this->components = $components;
            return;
        }

        $registryFile = GeneralUtility::getFileAbsFileName('EXT:docs/Resources/Private/Registry/registry.yaml');

        if (!is_file($registryFile)) {
            return;
        }

        // Narrowed immediately below via is_array() - registry.yaml is hand-maintained, so its
        // shape is deliberately not trusted any further than "some array of some shape".
        // @mago-expect analysis:mixed-assignment
        $data = Yaml::parseFile($registryFile);
        if (!is_array($data)) {
            return;
        }

        $baseDir = dirname($registryFile) . '/';

        // Narrowed immediately below via is_array() - registry.yaml is hand-maintained, so its
        // shape is deliberately not trusted any further than "some array of some shape".
        // @mago-expect analysis:mixed-assignment
        foreach ($data as $key => $config) {
            $key = (string)$key;
            if (!is_array($config) || !is_array($config['files'] ?? null) || $config['files'] === []) {
                continue;
            }

            $componentDir = $baseDir . Typed::string($config['name'] ?? null) . '/';
            if (!is_dir($componentDir)) {
                continue;
            }

            $files = [];
            // Narrowed immediately below via is_string() - same reasoning as above.
            // @mago-expect analysis:mixed-assignment
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
                name: Typed::string($config['name'] ?? null),
                basePath: $componentDir,
                files: $files,
                meta: $config,
            );
        }

        $this->cache->set('components', $this->components);
    }
}
