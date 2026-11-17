<?php

declare(strict_types=1);

namespace FluidPrimitives\Demos\Controller;

use FluidPrimitives\Demos\Domain\Dto\AccountSettings;
use FluidPrimitives\Demos\Service\CurrentUser;
use Jramke\FluidPrimitives\Traits\JsonValidationErrorsTrait;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * The account settings of the logged-in user, the target of the user menu in the site header.
 */
final class AccountController extends ActionController
{
    use JsonValidationErrorsTrait;

    public function __construct(
        private readonly ConnectionPool $connectionPool,
    ) {}

    public function editAction(): ResponseInterface
    {
        $this->view->assign('user', CurrentUser::fromRequest($this->request));

        return $this->htmlResponse();
    }

    public function updateAction(AccountSettings $account): ResponseInterface
    {
        // The record is the one of the session, never one the request names
        $user = CurrentUser::fromRequest($this->request);
        if ($user === null) {
            $this->respond(['message' => 'Your session has ended. Log in again.'], 403);
        }

        $name = trim($account->name);
        if ($name === '') {
            $this->respond(['account.name' => ['Please enter your name.']], 422);
        }

        $this->connectionPool->getConnectionForTable('fe_users')->update(
            'fe_users',
            ['name' => $name, 'tstamp' => time()],
            ['uid' => $user['uid']],
        );

        $this->respond(['message' => 'Your name is saved.']);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function respond(array $payload, int $status = 200): never
    {
        $response = $this->jsonResponse(json_encode($payload, JSON_THROW_ON_ERROR))->withStatus($status);
        throw new PropagateResponseException($response, $status);
    }
}
