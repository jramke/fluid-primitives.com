<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Controller;

use FluidPrimitives\Docs\Services\EventArchive;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * The JSON side of the list action of the fetch example: the docs context renders the list itself.
 */
final class EventArchiveController extends ActionController
{
    public function listAction(int $currentPage = 1): ResponseInterface
    {
        $events = [];
        // @mago-expect analysis:mixed-assignment
        foreach (EventArchive::paginate($currentPage)->getPaginatedItems() as $event) {
            $events[] = $event;
        }

        // The docs page renders this plugin inside a page, the response has to leave it
        $response = $this->jsonResponse(json_encode(['events' => $events]) ?: null);
        throw new PropagateResponseException($response, 200);
    }
}
