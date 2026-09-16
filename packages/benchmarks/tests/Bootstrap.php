<?php

declare(strict_types=1);

/**
 * Bootstrap for unit tests.
 *
 * Handles both contexts:
 * - Running from package directory: loads package vendor autoloader
 * - Running from monorepo root: autoloader already loaded, skip
 */

if (!class_exists(\TYPO3\TestingFramework\Core\Unit\UnitTestCase::class)) {
    require dirname(__DIR__) . '/vendor/autoload.php';
}
