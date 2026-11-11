<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Controller;

use FluidPrimitives\Docs\Services\EventArchive;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

final class EventArchiveController extends ActionController
{
    public function pageAction(int $page = 1): ResponseInterface
    {
        $events = iterator_to_array(EventArchive::paginate($page)->getPaginatedItems(), preserve_keys: false);

        $response = $this->jsonResponse(json_encode(['events' => $events]) ?: null);
        throw new PropagateResponseException($response, 200);
    }
}
