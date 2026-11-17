<?php

declare(strict_types=1);

use FluidPrimitives\Demos\Controller\AccountController;
use FluidPrimitives\Demos\Controller\RegistrationController;
use FluidPrimitives\Demos\Controller\SiteHeaderController;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

// @mago-expect analysis:unused-statement
defined('TYPO3') || die('Access denied.');

ExtensionUtility::configurePlugin(
    'Demos',
    'Registration',
    [RegistrationController::class => 'form,create'],
    [RegistrationController::class => 'create'],
);

// Both show the logged-in user, so neither is cached
ExtensionUtility::configurePlugin(
    'Demos',
    'SiteHeader',
    [SiteHeaderController::class => 'show'],
    [SiteHeaderController::class => 'show'],
);

ExtensionUtility::configurePlugin(
    'Demos',
    'Account',
    [AccountController::class => 'edit,update'],
    [AccountController::class => 'edit,update'],
);

// The category filter of the news demo is part of a GET form, which has no cHash. EXT:news excludes its search
// arguments the same way, the search result plugin is uncached.
// @mago-expect lint:no-global
// @mago-expect analysis:mixed-array-assignment(4)
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = '^tx_news_pi1[overwriteDemand][';
