<?php

declare(strict_types=1);

namespace FluidPrimitives\Demos\Controller;

use FluidPrimitives\Demos\Domain\Dto\Registration;
use FluidPrimitives\Demos\Domain\Validator\RegistrationValidator;
use Jramke\FluidPrimitives\Traits\JsonValidationErrorsTrait;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Extbase\Attribute\Validate;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Creates a frontend user in the storage folder of the plugin, who then logs in with felogin.
 */
final class RegistrationController extends ActionController
{
    use JsonValidationErrorsTrait;

    public function __construct(
        private readonly ConnectionPool $connectionPool,
        private readonly PasswordHashFactory $passwordHashFactory,
    ) {}

    public function formAction(): ResponseInterface
    {
        $this->view->assign('minPasswordLength', RegistrationValidator::MIN_PASSWORD_LENGTH);

        return $this->htmlResponse();
    }

    public function createAction(
        #[Validate(validator: RegistrationValidator::class)]
        Registration $registration,
    ): ResponseInterface {
        $storagePid = $this->getStoragePid();

        if ($this->isRegistered($registration->email)) {
            $this->respond(['registration.email' => ['This email address is already registered.']], 422);
        }

        $this->connectionPool->getConnectionForTable('fe_users')->insert('fe_users', [
            'pid' => $storagePid,
            'tstamp' => time(),
            'crdate' => time(),
            'username' => $registration->email,
            'email' => $registration->email,
            'name' => trim($registration->name),
            'password' => $this->passwordHashFactory
                ->getDefaultHashInstance('FE')
                ->getHashedPassword($registration->password),
            'usergroup' => (string)$this->getUserGroup($storagePid),
        ]);

        $this->respond([
            'message' => 'Your account is ready. Log in with your email address and password.',
        ]);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function respond(array $payload, int $status = 200): never
    {
        $response = $this->jsonResponse(json_encode($payload, JSON_THROW_ON_ERROR))->withStatus($status);
        throw new PropagateResponseException($response, $status);
    }

    private function getStoragePid(): int
    {
        $configuration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);

        return (int)($configuration['persistence']['storagePid'] ?? 0);
    }

    private function isRegistered(string $email): bool
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('fe_users');

        return (
            (int)$queryBuilder
                ->count('uid')
                ->from('fe_users')
                ->where($queryBuilder->expr()->eq('username', $queryBuilder->createNamedParameter($email)))
                ->executeQuery()
                ->fetchOne() > 0
        );
    }

    private function getUserGroup(int $storagePid): int
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('fe_groups');

        return (int)$queryBuilder
            ->select('uid')
            ->from('fe_groups')
            ->where($queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($storagePid)))
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchOne();
    }
}
