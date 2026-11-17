<?php

declare(strict_types=1);

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

// @mago-expect analysis:unused-statement
defined('TYPO3') || die('Access denied.');

ExtensionUtility::registerPlugin('Demos', 'Registration', 'Demos: Registration');
ExtensionUtility::registerPlugin('Demos', 'SiteHeader', 'Demos: Site header');
ExtensionUtility::registerPlugin('Demos', 'UserMenu', 'Demos: User menu');
ExtensionUtility::registerPlugin('Demos', 'Account', 'Demos: Account settings');
