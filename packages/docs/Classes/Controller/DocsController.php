<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Controller;

use FluidPrimitives\Docs\Domain\Model\EventRegistration;
use FluidPrimitives\Docs\PageTitle\DocsPageTitleProvider;
use FluidPrimitives\Docs\Services\NavigationBuilder;
use FluidPrimitives\Docs\Utility\DocsUtility;
use Jramke\FluidPrimitives\Traits\AjaxValidationTrait;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextFactory;

final class DocsController extends ActionController
{
    use AjaxValidationTrait;

    public function __construct(
        private readonly NavigationBuilder $navigationBuilder,
        private readonly RenderingContextFactory $renderingContextFactory,
        private readonly DocsPageTitleProvider $pageTitleProvider,
    ) {}

    public function showAction(string $path = ''): ResponseInterface
    {
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
            $this->pageTitleProvider->setTitle('Playground – Fluid Primitives');
            return $this->htmlResponse();
        }

        $baseDir = GeneralUtility::getFileAbsFileName('EXT:docs/Resources/Private/Content/');
        $filePath = $baseDir . rtrim($path, '/') . '.md';

        if (!is_file($filePath)) {
            $redirects = Yaml::parseFile($baseDir . 'redirects.yaml') ?? [];
            $target = $redirects[rtrim($path, '/')] ?? null;
            if ($target) {
                return $this->redirectToUri($target, 302);
            }

            $this->view->assign('layout', '404');
            $this->pageTitleProvider->setTitle('Not Found – Fluid Primitives');
            return $this->htmlResponse()->withStatus(404);
        }

        [$meta, $markdown] = $this->parseMarkdownFile($filePath);
        [$content, $toc] = DocsUtility::MarkdownToHtml($markdown, $this->request);

        $this->view->assignMultiple([
            'content' => $content,
            'toc' => $toc,
            'nav' => $this->navigationBuilder->buildNavigation($baseDir, $baseDir . 'nav.yaml'),
            'meta' => $meta,
            'path' => '/' . $path,
        ]);

        $this->pageTitleProvider->setTitle(($meta['title'] ?? 'Documentation') . ' – Fluid Primitives');

        return $this->htmlResponse();
    }

    public function registrationAction(EventRegistration $eventRegistration): ResponseInterface
    {
        $payload = ['success' => true];
        $status = 200;

        if ($eventRegistration->getTicketType() === 'vip') {
            $payload = ['eventRegistration.ticketType' => ['VIP tickets are sold out.']];
            $status = 422;
        }

        try {
            // do something with the registration
            // $this->eventRegistrationRepository->save($eventRegistration);
            // throw new \RuntimeException('Simulated server error for demonstration purposes.');
        } catch (\Exception) {
            $payload = ['success' => false, 'message' => 'An unexpected error occurred. Please try again later.'];
            $status = 500;
        }

        $json = json_encode($payload);
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

        $json = json_encode($payload);
        $response = $this->jsonResponse($json)->withStatus($status);
        throw new PropagateResponseException($response, $status);
    }

    #[\Override]
    protected function errorAction(): ResponseInterface
    {
        $this->throwJsonValidationErrorResponse();
        return parent::errorAction();
    }

    private function parseMarkdownFile(string $filePath): array
    {
        $content = file_get_contents($filePath);

        if (preg_match('/^---\n(.*?)\n---\n/s', $content, $matches)) {
            $yaml = $matches[1];
            $meta = Yaml::parse($yaml) ?? [];
            $markdown = substr($content, strlen($matches[0]));
        } else {
            $meta = [];
            $markdown = $content;
        }

        if (($meta['title'] ?? '') === '' && preg_match('/^#\s+(.+)$/m', $markdown, $h1Match)) {
            $meta['title'] = trim($h1Match[1]);
        }

        return [$meta, $markdown];
    }
}
