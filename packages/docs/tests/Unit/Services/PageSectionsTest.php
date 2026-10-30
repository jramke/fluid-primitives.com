<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Tests\Unit\Services;

use FluidPrimitives\Docs\Services\PageSections;
use Jramke\FluidPrimitives\Domain\Dto\TocItem;
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
    public function splitsAtTheHeadings(): void
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
        // A page without a `##` heading has nothing to split
        self::assertSame(['intro' => '<p>x</p>', 'sections' => []], PageSections::split('<p>x</p>'));
    }

    #[Test]
    public function listsOnlyTheSectionsInTheTableOfContentsOfAnAccordionPage(): void
    {
        $html = $this->section('v1', '<p>c</p>');
        $toc = [new TocItem('v1', 2, 'v1'), new TocItem('core', 3, 'Core')];

        self::assertSame(
            ['v1'],
            array_map(static fn(TocItem $i) => $i->value, PageSections::forPage('docs/releases', $html, $toc)['toc']),
        );

        $other = PageSections::forPage('docs/introduction', $html, $toc);
        self::assertSame($toc, $other['toc']);
        self::assertSame([], $other['sections']);
        self::assertSame($html, $other['intro']);
    }
}
