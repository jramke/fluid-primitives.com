<?php

declare(strict_types=1);

namespace FluidPrimitives\Demos\Middleware;

use Jramke\FluidPrimitives\Utility\Typed;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\JsonResponse;

/**
 * The autocomplete of the search demo: the titles in the ke_search index that contain the typed text. The free
 * ke_search has no autocomplete of its own.
 */
final readonly class SearchSuggestions implements MiddlewareInterface
{
    public const string PATH = '/demos/search/suggestions';

    public function __construct(
        private ConnectionPool $connectionPool,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getUri()->getPath() !== self::PATH) {
            return $handler->handle($request);
        }

        $query = trim(mb_substr(Typed::string($request->getQueryParams()['q'] ?? null), start: 0, length: 100));
        if (mb_strlen($query) < 2) {
            return new JsonResponse([]);
        }

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tx_kesearch_index');
        $titles = $queryBuilder
            ->select('title')
            ->from('tx_kesearch_index')
            ->where(
                $queryBuilder->expr()->like(
                    'title',
                    $queryBuilder->createNamedParameter('%' . $queryBuilder->escapeLikeWildcards($query) . '%'),
                ),
                $queryBuilder->expr()->in('fe_group', $queryBuilder->createNamedParameter(
                    ['', '0'],
                    Connection::PARAM_STR_ARRAY,
                )),
            )
            ->groupBy('title')
            ->orderBy('title')
            ->setMaxResults(8)
            ->executeQuery()
            ->fetchFirstColumn();

        return new JsonResponse(array_map(static fn(mixed $title): array => [
            'value' => Typed::string($title),
        ], $titles));
    }
}
