<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Services;

use TYPO3\CMS\Core\Pagination\ArrayPaginator;

/**
 * The demo events of the pagination examples, paginated as a list action would.
 */
final class EventArchive
{
    private const array EVENTS = [
        'Open-air concert',
        'Christmas market',
        'Wine tasting',
        'Guided city tour',
        'Jazz evening',
        'Farmers market',
    ];

    private const array CITIES = ['Berlin', 'Hamburg', 'Munich', 'Cologne', 'Leipzig', 'Dresden', 'Bremen', 'Freiburg'];

    public static function paginate(int $currentPage): ArrayPaginator
    {
        $events = [];
        foreach (self::CITIES as $city) {
            foreach (self::EVENTS as $title) {
                $events[] = ['title' => $title, 'city' => $city];
            }
        }

        return new ArrayPaginator($events, max(1, $currentPage), 4);
    }
}
