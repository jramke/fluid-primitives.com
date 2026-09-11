<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Controller;

use FluidPrimitives\Docs\Domain\Model\EventRegistration;
use FluidPrimitives\Docs\Domain\Repository\EventRegistrationRepository;
use FluidPrimitives\Docs\Domain\Validator\EventRegistrationValidator;
use Jramke\FluidPrimitives\Traits\AjaxValidationTrait;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Extbase\Attribute\Validate;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

final class EventRegistrationController extends ActionController
{
    use AjaxValidationTrait;

    public function __construct(
        private readonly EventRegistrationRepository $eventRegistrationRepository,
        private readonly PersistenceManagerInterface $persistenceManager,
    ) {}

    public function registrationAction(
        #[Validate(validator: EventRegistrationValidator::class)]
        EventRegistration $eventRegistration,
    ): ResponseInterface {
        // Reject VIP tickets as a server-side business rule without a validator.
        // Simply moving this into the validator and calling addErrorForProperty would result in the same behavior.
        if ($eventRegistration->getTicketType() === 'vip') {
            $payload = ['eventRegistration.ticketType' => ['VIP tickets are sold out.']];
            $response = $this->jsonResponse(json_encode($payload))->withStatus(422);
            throw new PropagateResponseException($response, 422);
        }

        $this->eventRegistrationRepository->add($eventRegistration);
        // Flushed explicitly since we escape the normal response cycle via PropagateResponseException
        // below - Extbase's own end-of-request persistAll would otherwise never run.
        $this->persistenceManager->persistAll();

        // Send confirmation email, etc.

        $response = $this->jsonResponse(json_encode([
            'success' => true,
            'message' => 'Your registration was submitted successfully. Thank you.',
        ]))->withStatus(200);
        throw new PropagateResponseException($response, 200);
    }

    #[\Override]
    protected function errorAction(): ResponseInterface
    {
        $this->throwJsonValidationErrorResponse();
        return parent::errorAction();
    }
}
