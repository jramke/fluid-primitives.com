<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\CommonMark;

use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\NodeIterator;
use League\CommonMark\Node\RawMarkupContainerInterface;
use League\CommonMark\Node\StringContainerHelper;

/**
 * The headings of a page for its table of contents: the ones that got an id from the permalink extension, which is
 * where the table of contents links to.
 */
final class PageHeadings
{
    /**
     * @return list<array{value: string, depth: int, label: string}>
     */
    public static function collect(Document $document): array
    {
        $headings = [];
        foreach ($document->iterator(NodeIterator::FLAG_BLOCKS_ONLY) as $node) {
            if (!$node instanceof Heading) {
                continue;
            }

            // The data of a node is untyped, is_string() below narrows it.
            // @mago-expect analysis:mixed-assignment
            $id = $node->data->get('attributes/id', '');
            if (!is_string($id) || $id === '') {
                continue;
            }

            $headings[] = [
                'value' => $id,
                'depth' => $node->getLevel(),
                'label' => StringContainerHelper::getChildText($node, [RawMarkupContainerInterface::class]),
            ];
        }

        return $headings;
    }
}
