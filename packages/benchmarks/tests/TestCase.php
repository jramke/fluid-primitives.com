<?php

declare(strict_types=1);

namespace FluidPrimitives\Benchmarks\Tests;

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Base test case for unit tests - isolated PHP classes with no TYPO3 request/DI context.
 */
abstract class TestCase extends UnitTestCase {}
