<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Tests\Unit\Services;

use FluidPrimitives\Docs\Services\CollapsibleSections;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class CollapsibleSectionsTest extends UnitTestCase
{
    private function section(string $title, string $body): string
    {
        $id = strtolower($title);

        return (
            '<h2 id="' .
            $id .
            '">' .
            $title .
            '<a href="#' .
            $id .
            '" class="heading-permalink" title=""></a></h2>' .
            $body
        );
    }

    #[Test]
    public function wrapsEverySectionAndOpensTheNewestOnes(): void
    {
        $html =
            '<h1>Changelog</h1><p>Intro</p>' .
            $this->section('Unreleased', '<ul><li>a</li></ul>') .
            $this->section('v2', '<p><em>2026-10-30</em></p><ul><li>b</li></ul>') .
            $this->section('v1', '<p><em>2026-10-12</em></p><ul><li>c</li></ul>');

        $wrapped = CollapsibleSections::wrap($html);

        self::assertStringStartsWith('<h1>Changelog</h1><p>Intro</p><details', $wrapped);
        self::assertSame(3, substr_count($wrapped, needle: '<details'));
        self::assertStringContainsString(
            '<details class="collapsible-section" open><summary><h2 id="unreleased">',
            $wrapped,
        );
        self::assertStringContainsString(
            '<details class="collapsible-section" open><summary><h2 id="v2">v2</h2><time datetime="2026-10-30">2026-10-30</time></summary><div><ul><li>b</li></ul></div>',
            $wrapped,
        );
        self::assertStringContainsString('<details class="collapsible-section"><summary><h2 id="v1">', $wrapped);
        self::assertStringNotContainsString('<p><em>2026', $wrapped);

        // Without an Unreleased section only the newest release opens
        $released = CollapsibleSections::wrap($this->section('v2', '<p>b</p>') . $this->section('v1', '<p>c</p>'));
        self::assertSame(1, substr_count($released, needle: ' open>'));
    }
}
