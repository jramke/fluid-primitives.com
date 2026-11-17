<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\EventListener;

use Jramke\FluidPrimitives\Utility\Typed;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Authentication\Event\AfterUserLoggedInEvent;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * The demos run on a public site: everybody can register. A login removes the accounts of its folder that are
 * older than a day.
 */
#[AsEventListener]
final readonly class RemoveExpiredDemoAccounts
{
    private const int LIFETIME = 86400;

    public function __construct(
        private ConnectionPool $connectionPool,
    ) {}

    public function __invoke(AfterUserLoggedInEvent $event): void
    {
        $authentication = $event->getUser();
        if (!$authentication instanceof FrontendUserAuthentication) {
            return;
        }

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('fe_users');
        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder
            ->delete('fe_users')
            ->where(
                $queryBuilder->expr()->eq(
                    'pid',
                    $queryBuilder->createNamedParameter(Typed::int($authentication->user['pid'] ?? null)),
                ),
                $queryBuilder->expr()->lt('crdate', $queryBuilder->createNamedParameter(time() - self::LIFETIME)),
            )
            ->executeStatement();
    }
}
