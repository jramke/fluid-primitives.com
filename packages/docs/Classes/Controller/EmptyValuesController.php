<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Controller;

use FluidPrimitives\Docs\Domain\Dto\EmptyValues;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * The endpoints of the playground's empty-values test forms: they log what was sent and what Extbase
 * mapped it to, once as a plain array and once as a DTO.
 */
final class EmptyValuesController extends ActionController
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * @param array<array-key, mixed> $data
     */
    public function arrayAction(array $data = []): ResponseInterface
    {
        return $this->respond($data);
    }

    public function dtoAction(EmptyValues $data): ResponseInterface
    {
        return $this->respond($data);
    }

    private function respond(mixed $data): ResponseInterface
    {
        if (!Environment::getContext()->isDevelopment()) {
            throw new PropagateResponseException($this->htmlResponse('')->withStatus(404), 404);
        }

        krexxlog($this->request->getParsedBody());
        krexxlog($data);
        $this->logger->warning('Empty values form', [
            'body' => $this->request->getParsedBody(),
            'argument' => json_encode($data, JSON_PARTIAL_OUTPUT_ON_ERROR),
        ]);

        throw new PropagateResponseException($this->jsonResponse('{"success":true}'), 200);
    }
}
