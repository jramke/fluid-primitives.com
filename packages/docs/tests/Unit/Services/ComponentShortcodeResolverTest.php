<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Tests\Unit\Services;

use FluidPrimitives\Docs\Services\ComponentShortcodeResolver;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class ComponentShortcodeResolverTest extends UnitTestCase
{
    #[Test]
    public function stripRemovesOnlyTheNamedComponents(): void
    {
        $markdown = <<<'MD'
        Intro

        {% component: "ui:componentExample", arguments: {
            "name": "basic"
        } %}

        {% component: "ui:alert", arguments: {"title": "Heads up"} %}

        {% component: "ui:componentExample" %}

        Outro
        MD;

        $result = ComponentShortcodeResolver::strip($markdown, ['ui:componentExample']);

        self::assertStringNotContainsString('ui:componentExample', $result);
        self::assertStringNotContainsString('basic', $result);
        self::assertStringContainsString('{% component: "ui:alert", arguments: {"title": "Heads up"} %}', $result);
        self::assertStringContainsString('Intro', $result);
        self::assertStringContainsString('Outro', $result);
    }
}
