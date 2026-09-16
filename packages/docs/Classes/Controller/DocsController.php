<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Controller;

use FluidPrimitives\Docs\Domain\Model\EventRegistration;
use FluidPrimitives\Docs\Domain\Repository\EventRegistrationRepository;
use FluidPrimitives\Docs\Domain\Validator\EventRegistrationValidator;
use FluidPrimitives\Docs\PageTitle\DocsPageTitleProvider;
use FluidPrimitives\Docs\Services\DocsMarkdownModeResponder;
use FluidPrimitives\Docs\Services\NavigationBuilder;
use FluidPrimitives\Docs\Services\ViewAsMarkdownLinkInjector;
use FluidPrimitives\Docs\Utility\DocsUtility;
use Jramke\FluidPrimitives\Traits\AjaxValidationTrait;
use Jramke\FluidPrimitives\Utility\Typed;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Attribute\Validate;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

final class DocsController extends ActionController
{
    use AjaxValidationTrait;

    private const array MAIN_LINKS = [
        ['label' => 'The Pitch', 'href' => '/the-pitch'],
        ['label' => 'Documentation', 'href' => '/docs'],
        ['label' => 'GitHub', 'href' => '/github', 'external' => true],
    ];

    public function __construct(
        private readonly NavigationBuilder $navigationBuilder,
        private readonly DocsPageTitleProvider $pageTitleProvider,
        private readonly EventRegistrationRepository $eventRegistrationRepository,
        private readonly PersistenceManagerInterface $persistenceManager,
        private readonly DocsMarkdownModeResponder $markdownModeResponder,
    ) {}

    public function showAction(string $path = ''): ResponseInterface
    {
        $this->view->assign('mainLinks', self::MAIN_LINKS);

        if ($path === '') {
            $this->view->assign('layout', 'home');
            $this->pageTitleProvider->setTitle('Fluid Primitives – The headless component library for TYPO3 Fluid');
            return $this->htmlResponse();
        }

        if ($path === 'playground' && Environment::getContext()->isDevelopment()) {
            $this->view->assign('layout', 'playground');
            $test = new EventRegistration();
            // $test->setEmail('test@example.com');
            // $test->setTicketType('vip');
            // $test->setName('John Doe');
            // $test->setMode('virtual');
            $this->view->assign('defaultEventRegistration', $test);

            // Manual test helper for the edit form: set a uid here to load an existing registration
            // to edit, e.g. <ui:editEventRegistration object="{editEventRegistration}" /> in
            // Playground.html. A plain query param isn't used here since it would need a cHash
            // exclusion to not 404 on this cached page - editing the uid below is simpler for a
            // dev-only manual test helper.
            $editUid = 0;
            // @mago-expect analysis:redundant-comparison
            // @mago-expect analysis:impossible-condition
            if ($editUid > 0) {
                // @mago-expect analysis:no-value
                $this->view->assign('editEventRegistration', $this->eventRegistrationRepository->findByUid($editUid));
            }

            $this->pageTitleProvider->setTitle('Playground – Fluid Primitives');
            return $this->htmlResponse();
        }

        $baseDir = GeneralUtility::getFileAbsFileName('EXT:docs/Resources/Private/Content/');
        $filePath = $baseDir . rtrim($path, characters: '/') . '.md';

        if (!is_file($filePath)) {
            $redirects = Typed::arrayOrNull(Yaml::parseFile($baseDir . 'redirects.yaml')) ?? [];
            $target = Typed::stringOrNull($redirects[rtrim($path, characters: '/')] ?? null);
            if ($target !== null) {
                return $this->redirectToUri(
                    $this->markdownModeResponder->redirectTarget($target, $this->request),
                    statusCode: 302,
                );
            }

            // PropagateResponseException, not a plain return - this Extbase action is embedded in the
            // page's normal PAGEVIEW/layout rendering, which would otherwise wrap the response body in
            // the full HTML page shell (see registrationAction() below).
            $this->markdownModeResponder->respondNotFoundIfActive($this->request);

            $this->view->assign('layout', '404');
            $this->pageTitleProvider->setTitle('Not Found – Fluid Primitives');
            return $this->htmlResponse()->withStatus(404);
        }

        [$meta, $markdown] = $this->parseMarkdownFile($filePath);

        $this->markdownModeResponder->respondWithContentIfActive($markdown, $this->request);

        [$content, $toc] = DocsUtility::MarkdownToHtml($markdown, $this->request);
        $content = ViewAsMarkdownLinkInjector::inject($content, '/' . $path);

        $this->view->assignMultiple([
            'content' => $content,
            'toc' => $toc,
            'nav' => $this->navigationBuilder->buildNavigation($baseDir, $baseDir . 'nav.yaml'),
            'meta' => $meta,
            'path' => '/' . $path,
        ]);

        $this->pageTitleProvider->setTitle(
            Typed::string($meta['title'] ?? null, 'Documentation') . ' – Fluid Primitives',
        );

        return $this->htmlResponse();
    }

    public function registrationAction(
        #[Validate(validator: EventRegistrationValidator::class)]
        EventRegistration $eventRegistration,
    ): ResponseInterface {
        $payload = ['success' => true];
        $status = 200;

        if ($eventRegistration->getTicketType() === 'vip') {
            $payload = ['eventRegistration.ticketType' => ['VIP tickets are sold out.']];
            $status = 422;
        }

        if ($status === 200) {
            try {
                // A submission carrying a signed `__identity` (see `FormContext::renderHiddenIdentityField()`)
                // is mapped by Extbase onto the already-persisted entity it identifies, so `_isNew()`
                // is false here for the edit form; a plain new registration has no identity and is still new.
                $eventRegistration->_isNew()
                    ? $this->eventRegistrationRepository->add($eventRegistration)
                    : $this->eventRegistrationRepository->update($eventRegistration);
                // Flushed explicitly (rather than left to Extbase's own end-of-request persistAll)
                // because this action escapes the normal response cycle via PropagateResponseException
                // below - we want a persistence failure to surface as a 500 here, not silently
                // after we've already told the client it succeeded.
                $this->persistenceManager->persistAll();
            } catch (\Exception) {
                $payload = ['success' => false, 'message' => 'An unexpected error occurred. Please try again later.'];
                $status = 500;
            }
        }

        $json = json_encode($payload) ?: null;
        $response = $this->jsonResponse($json)->withStatus($status);
        throw new PropagateResponseException($response, $status); // or return $response; if standalone plugin
    }

    public function homepageAction(string $homepage = ''): ResponseInterface
    {
        $payload = [
            'success' => true,
            'message' => sprintf('Submitted homepage: %s', $homepage),
        ];
        $status = 200;

        if ($homepage === 'https://down.example.com') {
            $payload = [
                'success' => false,
                'message' => 'The demo server is unavailable right now.',
            ];
            $status = 500;
        }

        $json = json_encode($payload) ?: null;
        $response = $this->jsonResponse($json)->withStatus($status);
        throw new PropagateResponseException($response, $status);
    }

    #[\Override]
    protected function errorAction(): ResponseInterface
    {
        $this->throwJsonValidationErrorResponse();
        return parent::errorAction();
    }

    /**
     * @return array{0: array<array-key, mixed>, 1: string}
     */
    private function parseMarkdownFile(string $filePath): array
    {
        $content = file_get_contents($filePath) ?: '';

        $meta = [];
        $markdown = $content;
        $matches = [];
        $h1Match = [];

        if (preg_match('/^---\n(.*?)\n---\n/s', $content, $matches)) {
            $meta = Typed::arrayOrNull(Yaml::parse($matches[1])) ?? [];
            $markdown = substr($content, strlen($matches[0]));
        }

        if (($meta['title'] ?? '') === '' && preg_match('/^#\s+(.+)$/m', $markdown, $h1Match)) {
            $meta['title'] = trim($h1Match[1]);
        }

        return [$meta, $markdown];
    }
}
