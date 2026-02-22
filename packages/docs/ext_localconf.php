<?php

defined('TYPO3') or die('Access denied.');

$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['vite'] = ['Praetorius\\ViteAssetCollector\\ViewHelpers'];

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Docs',
    'Docs',
    [
        \FluidPrimitives\Docs\Controller\DocsController::class => 'show,registration',
    ],
    [],
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);


$GLOBALS['TYPO3_CONF_VARS']['SYS']['routing']['aspects']['ValidatedPathMapper'] = \FluidPrimitives\Docs\Routing\Aspect\ValidatedPathMapper::class;

$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['ui'][] = 'FluidPrimitives\\Docs\\Components\\ComponentCollection';
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['docs'] = ['FluidPrimitives\\Docs\\ViewHelpers'];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['fluid_primitives_registry'] ??= [
    'frontend' => \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
    'backend' => \TYPO3\CMS\Core\Cache\Backend\FileBackend::class,
    'groups' => ['pages'],
];
