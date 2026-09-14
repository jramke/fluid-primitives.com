<?php

declare(strict_types=1);

use FluidPrimitives\Docs\Components\ComponentCollection;
use FluidPrimitives\Docs\Controller\CitySearchController;
use FluidPrimitives\Docs\Controller\DocsController;
use FluidPrimitives\Docs\Routing\Aspect\ValidatedPathMapper;
use TYPO3\CMS\Core\Cache\Backend\FileBackend;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

// @mago-expect analysis:unused-statement
defined('TYPO3') || die('Access denied.');

// @mago-expect lint:no-global
// @mago-expect analysis:mixed-array-assignment(4)
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['vite'] = ['Praetorius\\ViteAssetCollector\\ViewHelpers'];

ExtensionUtility::configurePlugin(
    'Docs',
    'Docs',
    [
        DocsController::class => 'show,registration,homepage',
        CitySearchController::class => 'search',
    ],
    [
        CitySearchController::class => 'search',
    ],
);

// @mago-expect lint:no-global
// @mago-expect analysis:mixed-array-assignment(4)
$GLOBALS['TYPO3_CONF_VARS']['SYS']['routing']['aspects']['ValidatedPathMapper'] = ValidatedPathMapper::class;

// @mago-expect lint:no-global
// @mago-expect analysis:mixed-array-assignment(5)
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['ui'][] = ComponentCollection::class;

// @mago-expect lint:no-global
// @mago-expect analysis:mixed-array-assignment(4)
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['docs'] = ['FluidPrimitives\\Docs\\ViewHelpers'];

// @mago-expect lint:no-global
// @mago-expect analysis:mixed-array-assignment(4)
$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['fluid_primitives_registry'] ??= [
    'frontend' => VariableFrontend::class,
    'backend' => FileBackend::class,
    'groups' => ['pages'],
];
