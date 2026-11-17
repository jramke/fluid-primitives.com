<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

// @mago-expect analysis:unused-statement
defined('TYPO3') || die('Access denied.');

// A demo of the docs: a page of the imaginary site of EXT:demos in a frame, the page is the link of the header
ExtensionManagementUtility::addRecordType([
    'label' => 'Demo frame',
    'value' => 'docs_demoframe',
    'icon' => 'content-special-html',
    'group' => 'special',
], '--palette--;;headers');
