<?php

declare(strict_types=1);

use FluidPrimitives\Docs\Components\ComponentCollection;
use FluidPrimitives\Docs\Controller\DocsController;
use FluidPrimitives\Docs\Routing\Aspect\ValidatedPathMapper;
use TYPO3\CMS\Core\Cache\Backend\FileBackend;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || die('Access denied.');

$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['vite'] = ['Praetorius\\ViteAssetCollector\\ViewHelpers'];

ExtensionUtility::configurePlugin(
    'Docs',
    'Docs',
    [
        DocsController::class => 'show,registration,homepage',
    ],
    [],
);

$GLOBALS['TYPO3_CONF_VARS']['SYS']['routing']['aspects']['ValidatedPathMapper'] = ValidatedPathMapper::class;

$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['ui'][] = ComponentCollection::class;
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['docs'] = ['FluidPrimitives\\Docs\\ViewHelpers'];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['fluid_primitives_registry'] ??= [
    'frontend' => VariableFrontend::class,
    'backend' => FileBackend::class,
    'groups' => ['pages'],
];
