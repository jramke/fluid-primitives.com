<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Controller;

use FluidPrimitives\Docs\Domain\Model\EventRegistration;
use Jramke\FluidPrimitives\Traits\AjaxValidationTrait;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

final class EventRegistrationController extends ActionController
{
    use AjaxValidationTrait;

    public function registrationAction(EventRegistration $eventRegistration): ResponseInterface
    {
        // Reject VIP tickets as a server-side business rule
        if ($eventRegistration->getTicketType() === 'vip') {
            $payload = ['eventRegistration.ticketType' => ['VIP tickets are sold out.']];
            $response = $this->jsonResponse(json_encode($payload))->withStatus(422);
            throw new PropagateResponseException($response, 422);
        }

        // Save the registration, send confirmation email, etc.
        // $this->eventRegistrationRepository->save($eventRegistration);

        $response = $this->jsonResponse(json_encode([
            'success' => true,
            'message' => 'Your registration was submitted successfully. Thank you.',
        ]))->withStatus(200);
        throw new PropagateResponseException($response, 200);
    }

    protected function errorAction(): ResponseInterface
    {
        $this->throwJsonValidationErrorResponse();
        return parent::errorAction();
    }
}
