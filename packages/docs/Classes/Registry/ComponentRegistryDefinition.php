<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Registry;

final class ComponentRegistryDefinition
{
    public function __construct(
        public readonly string $key,
        public readonly string $name,
        public readonly string $basePath,
        public readonly array $files,
        public readonly array $meta = [],
    ) {}
}
