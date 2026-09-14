<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

final class CitySearchController extends ActionController
{
    /** @var list<array{value: string, title: string, description: string}> */
    private const array CITIES = [
        ['value' => 'amsterdam', 'title' => 'Amsterdam', 'description' => 'Netherlands'],
        ['value' => 'athens', 'title' => 'Athens', 'description' => 'Greece'],
        ['value' => 'auckland', 'title' => 'Auckland', 'description' => 'New Zealand'],
        ['value' => 'barcelona', 'title' => 'Barcelona', 'description' => 'Spain'],
        ['value' => 'berlin', 'title' => 'Berlin', 'description' => 'Germany'],
        ['value' => 'boston', 'title' => 'Boston', 'description' => 'United States'],
        ['value' => 'brisbane', 'title' => 'Brisbane', 'description' => 'Australia'],
        ['value' => 'budapest', 'title' => 'Budapest', 'description' => 'Hungary'],
        ['value' => 'chicago', 'title' => 'Chicago', 'description' => 'United States'],
        ['value' => 'copenhagen', 'title' => 'Copenhagen', 'description' => 'Denmark'],
        ['value' => 'dublin', 'title' => 'Dublin', 'description' => 'Ireland'],
        ['value' => 'edinburgh', 'title' => 'Edinburgh', 'description' => 'United Kingdom'],
        ['value' => 'frankfurt', 'title' => 'Frankfurt', 'description' => 'Germany'],
        ['value' => 'geneva', 'title' => 'Geneva', 'description' => 'Switzerland'],
        ['value' => 'lisbon', 'title' => 'Lisbon', 'description' => 'Portugal'],
        ['value' => 'london', 'title' => 'London', 'description' => 'United Kingdom'],
        ['value' => 'los-angeles', 'title' => 'Los Angeles', 'description' => 'United States'],
        ['value' => 'madrid', 'title' => 'Madrid', 'description' => 'Spain'],
        ['value' => 'melbourne', 'title' => 'Melbourne', 'description' => 'Australia'],
        ['value' => 'mexico-city', 'title' => 'Mexico City', 'description' => 'Mexico'],
        ['value' => 'milan', 'title' => 'Milan', 'description' => 'Italy'],
        ['value' => 'montreal', 'title' => 'Montreal', 'description' => 'Canada'],
        ['value' => 'munich', 'title' => 'Munich', 'description' => 'Germany'],
        ['value' => 'new-york', 'title' => 'New York', 'description' => 'United States'],
        ['value' => 'oslo', 'title' => 'Oslo', 'description' => 'Norway'],
        ['value' => 'paris', 'title' => 'Paris', 'description' => 'France'],
        ['value' => 'prague', 'title' => 'Prague', 'description' => 'Czech Republic'],
        ['value' => 'rome', 'title' => 'Rome', 'description' => 'Italy'],
        ['value' => 'san-francisco', 'title' => 'San Francisco', 'description' => 'United States'],
        ['value' => 'sydney', 'title' => 'Sydney', 'description' => 'Australia'],
        ['value' => 'tokyo', 'title' => 'Tokyo', 'description' => 'Japan'],
        ['value' => 'vancouver', 'title' => 'Vancouver', 'description' => 'Canada'],
        ['value' => 'washington-dc', 'title' => 'Washington D.C.', 'description' => 'United States'],
        ['value' => 'zurich', 'title' => 'Zurich', 'description' => 'Switzerland'],
        ['value' => 'hamburg', 'title' => 'Hamburg', 'description' => 'Germany'],
        ['value' => 'helsinki', 'title' => 'Helsinki', 'description' => 'Finland'],
    ];

    public function searchAction(string $q = ''): ResponseInterface
    {
        $query = mb_strtolower($q);

        $results = $query === ''
            ? []
            : array_values(array_filter(self::CITIES, static fn(array $city): bool => str_contains(
                mb_strtolower($city['title']),
                $query,
            )));

        $shouldSleep = (bool)random_int(0, max: 1);
        if ($shouldSleep) {
            sleep(random_int(1, max: 2));
        }

        $response = $this->jsonResponse(json_encode($results) ?: null)->withStatus(200);
        throw new PropagateResponseException($response, 200);
    }
}
