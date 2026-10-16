<?php

declare(strict_types=1);

use Rector\PostRector\Rector\NameImportingPostRector;

// The rule sets live in the package config so its standalone CI and the monorepo share them.
return (require __DIR__ . '/packages/fluid-primitives/rector.php')
    ->withPaths([
        __DIR__ . '/config',
        __DIR__ . '/packages',
    ])
    ->withSkip([
        'Public/**',
        'node_modules/**',
        'config/system/settings.php',
        'packages/fluid-primitives/public',
        'packages/fluid-primitives/vendor',
        // @see https://github.com/sabbelasichon/typo3-rector/issues/2536
        __DIR__ . '/**/Configuration/ExtensionBuilder/*',
        NameImportingPostRector::class => [
            'ClassAliasMap.php',
        ],
    ]);
