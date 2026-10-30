<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Tests\Unit\Services;

use FluidPrimitives\Docs\Services\PageSections;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class PageSectionsTest extends UnitTestCase
{
    private function section(string $title, string $body): string
    {
        $id = strtolower($title);

        return (
            '<div class="permalink-wrapper"><h2 id="' .
            $id .
            '">' .
            $title .
            '</h2><a href="#' .
            $id .
            '" class="heading-permalink" title=""><span class="sr-only">Permalink to heading "' .
            $title .
            '"</span></a></div>' .
            $body
        );
    }

    #[Test]
    public function splitsAtTheHeadingsAndOpensTheNewestSections(): void
    {
        $html =
            '<h1>Changelog</h1><p>Intro</p>' .
            $this->section('Unreleased', '<ul><li>a</li></ul>') .
            $this->section('v2', '<p><em>2026-10-30</em></p><ul><li>b</li></ul>') .
            $this->section('v1', '<p><em>2026-10-12</em></p><ul><li>c</li></ul>');

        $result = PageSections::split($html);

        self::assertSame('<h1>Changelog</h1><p>Intro</p>', $result['intro']);
        self::assertSame(
            [
                ['id' => 'unreleased', 'title' => 'Unreleased', 'date' => '', 'body' => '<ul><li>a</li></ul>'],
                ['id' => 'v2', 'title' => 'v2', 'date' => '2026-10-30', 'body' => '<ul><li>b</li></ul>'],
                ['id' => 'v1', 'title' => 'v1', 'date' => '2026-10-12', 'body' => '<ul><li>c</li></ul>'],
            ],
            $result['sections'],
        );
        self::assertSame(['unreleased', 'v2'], $result['open']);

        // Without an Unreleased section only the newest release opens
        $released = PageSections::split($this->section('v2', '<p>b</p>') . $this->section('v1', '<p>c</p>'));
        self::assertSame(['v2'], $released['open']);

        // A page without a `##` heading has nothing to split
        self::assertSame(['intro' => '<p>x</p>', 'sections' => [], 'open' => []], PageSections::split('<p>x</p>'));
    }
}
