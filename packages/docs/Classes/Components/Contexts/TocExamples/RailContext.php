<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Components\Contexts\TocExamples;

use Jramke\FluidPrimitives\Contexts\AbstractComponentContext;

/**
 * The headings of the rail example, with the geometry of the rail drawn next to each link: a vertical line per
 * heading depth that bends into the line of the next one where the depth changes. The line of a link overlaps the
 * row above it by {@see self::BRIDGE} px, so a turn can straddle the boundary between two rows.
 */
final class RailContext extends AbstractComponentContext
{
    private const int BASE = 8;
    private const int RAIL_STEP = 8;
    private const int TEXT_STEP = 12;
    private const int MAX_LEVEL = 2;
    private const int BRIDGE = 6;

    private const array HEADINGS = [
        ['toc-rail-delivery',      2, 'Delivery',      'Orders placed before 2 pm on a working day ship the same day.'],
        ['toc-rail-returns',       2, 'Returns',       'You can return unworn items within 30 days.'],
        ['toc-rail-return-label',  3, 'Return label',  'Print the label from your order confirmation.'],
        ['toc-rail-return-window', 3, 'Return window', 'The 30 days start on the day the parcel arrives.'],
        ['toc-rail-payment',       2, 'Payment',       'We accept credit card, PayPal and invoice.'],
        ['toc-rail-credit-card',   3, 'Credit card',   'Your card is charged when the order ships.'],
        ['toc-rail-invoice',       3, 'Invoice',       'You get the invoice with the parcel.'],
        ['toc-rail-due-date',      4, 'Due date',      'Pay within 14 days of the invoice date.'],
        ['toc-rail-contact',       2, 'Contact',       'Write to support@example.com on weekdays.'],
    ];

    /**
     * @return list<array{value: string, depth: int, label: string, text: string, turns: bool, x: float, y1: int, d: string, svgStyle: string, linkStyle: string}>
     */
    public function getRails(): array
    {
        $rails = [];
        foreach (self::HEADINGS as $index => [$value, $depth, $label, $text]) {
            $line = $this->lineOffset($depth);
            $prevLine = $this->lineOffset(self::HEADINGS[$index - 1][1] ?? $depth);
            $nextLine = $this->lineOffset(self::HEADINGS[$index + 1][1] ?? $depth);
            $turns = $prevLine !== $line;

            $rails[] = [
                'value' => $value,
                'depth' => $depth,
                'label' => $label,
                'text' => $text,
                'turns' => $turns,
                'x' => $line + 0.5,
                'y1' => $turns ? self::BRIDGE * 2 : self::BRIDGE,
                'd' => sprintf('M %1$s 0 C %1$s 8 %2$s 4 %2$s %3$d', $prevLine + 0.5, $line + 0.5, self::BRIDGE * 2),
                'svgStyle' => sprintf(
                    'top: %dpx; width: %dpx; height: %s;',
                    -self::BRIDGE,
                    max($prevLine, $line) + 9,
                    $line === $nextLine ? 'calc(100% + ' . self::BRIDGE . 'px)' : '100%',
                ),
                'linkStyle' => 'padding-inline-start: ' . $this->textOffset($depth) . 'px;',
            ];
        }

        return $rails;
    }

    // h2 sits at level 0, deeper headings step in, and h5 and below share the indent of h4
    private function level(int $depth): int
    {
        return min(max($depth - 2, 0), self::MAX_LEVEL);
    }

    private function lineOffset(int $depth): int
    {
        return self::BASE + ($this->level($depth) * self::RAIL_STEP);
    }

    private function textOffset(int $depth): int
    {
        return self::BASE + (($this->level($depth) + 1) * self::TEXT_STEP);
    }
}
