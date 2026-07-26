<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Registry;

final readonly class ComponentRegistryDefinition
{
    public function __construct(
        public string $key,
        public string $name,
        public string $basePath,
        public array $files,
        public array $meta = [],
    ) {}
}
