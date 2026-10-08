<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\CommonMark;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Node\Inline\Text;

/**
 * Makes the `<h2 data-toc>` / `<h3 data-toc>` headings of a component's HTML real headings, so they get an id, a
 * permalink and an entry in the table of contents like the Markdown ones.
 *
 * A component resolves to one HTML block, which CommonMark leaves alone. Each marked heading splits that block in
 * two and takes its place between the halves: the nodes render in order, so the heading ends up where the template
 * wrote it, inside whatever wrapper the component opened.
 */
final class ComponentHeadingsExtension implements ExtensionInterface
{
    private const string HEADING_PATTERN = '/<h([23]) data-toc>(.*?)<\/h\1>/s';

    public function register(EnvironmentBuilderInterface $environment): void
    {
        // Before HeadingPermalink (-100) and TableOfContents (-150), which both read the headings.
        $environment->addEventListener(DocumentParsedEvent::class, $this->splitHtmlBlocks(...), 10);
    }

    private function splitHtmlBlocks(DocumentParsedEvent $event): void
    {
        $blocks = [];
        foreach ($event->getDocument()->iterator() as $node) {
            if ($node instanceof HtmlBlock && str_contains($node->getLiteral(), ' data-toc>')) {
                $blocks[] = $node;
            }
        }

        foreach ($blocks as $block) {
            $parts = preg_split(self::HEADING_PATTERN, $block->getLiteral(), flags: PREG_SPLIT_DELIM_CAPTURE) ?: [];

            // [html, level, heading, html, level, heading, ..., html]
            foreach (array_chunk($parts, length: 3) as $chunk) {
                [$html, $level, $title] = array_pad($chunk, length: 3, value: null);
                $block->insertBefore($this->htmlBlock((string)$html));
                if ($level !== null) {
                    $block->insertBefore($this->heading((int)$level, (string)$title));
                }
            }
            $block->detach();
        }
    }

    private function htmlBlock(string $html): HtmlBlock
    {
        $block = new HtmlBlock(HtmlBlock::TYPE_6_BLOCK_ELEMENT);
        $block->setLiteral($html);

        return $block;
    }

    private function heading(int $level, string $title): Heading
    {
        $heading = new Heading($level);
        $heading->appendChild(new Text(html_entity_decode($title)));

        return $heading;
    }
}
