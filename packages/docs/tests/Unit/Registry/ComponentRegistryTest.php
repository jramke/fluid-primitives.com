<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Tests\Unit\Registry;

use FluidPrimitives\Docs\Registry\ComponentRegistry;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class ComponentRegistryTest extends UnitTestCase
{
    private function registry(): ComponentRegistry
    {
        $cacheManager = $this->createStub(CacheManager::class);
        $cacheManager->method('getCache')->willReturn($this->createStub(VariableFrontend::class));

        return new ComponentRegistry($cacheManager, __DIR__ . '/../../Fixtures/Registry/registry.yaml');
    }

    /**
     * @return iterable<string, array{0: string, 1: string}>
     */
    public static function multiWordSpellings(): iterable
    {
        yield 'camelCase key' => ['datePicker', 'datePicker'];
        yield 'kebab-case spelling of a camelCase key' => ['date-picker', 'datePicker'];
        yield 'camelCase spelling of a legacy kebab-case key' => ['checkboxGroup', 'checkboxGroup'];
        yield 'kebab-case spelling of a legacy kebab-case key' => ['checkbox-group', 'checkboxGroup'];
    }

    #[Test]
    #[DataProvider('multiWordSpellings')]
    public function bothSpellingsResolveToTheSameCamelCaseEntry(string $spelling, string $expectedKey): void
    {
        $registry = $this->registry();

        self::assertTrue($registry->has($spelling));
        self::assertSame($expectedKey, $registry->get($spelling)->key);
    }

    #[Test]
    public function aSingleWordKeyIsUnaffected(): void
    {
        $registry = $this->registry();

        self::assertTrue($registry->has('button'));
        self::assertSame('Button', $registry->get('button')->name);
    }

    #[Test]
    public function anUnknownKeyIsStillUnknown(): void
    {
        $registry = $this->registry();

        self::assertFalse($registry->has('date-pickers'));
        $this->expectException(\InvalidArgumentException::class);
        $registry->get('nope');
    }

    #[Test]
    public function listExposesTheCamelCaseKeys(): void
    {
        $keys = array_map(static fn($definition) => $definition->key, $this->registry()->list());

        self::assertSame(['button', 'checkboxGroup', 'datePicker'], $keys);
    }
}
