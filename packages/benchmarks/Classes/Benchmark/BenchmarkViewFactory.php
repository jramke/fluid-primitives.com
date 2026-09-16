<?php

declare(strict_types=1);

namespace FluidPrimitives\Benchmarks\Benchmark;

use FluidPrimitives\Benchmarks\Component\BenchComponentCollection;
use FluidPrimitives\Benchmarks\Component\VanillaComponentCollection;
use Jramke\FluidPrimitives\Component\ComponentPrimitivesCollection;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Core\View\ViewInterface;

/**
 * Builds the Fluid view used to render a Scenario template. Deliberately goes through TYPO3's
 * own `ViewFactoryInterface` (which wires the view through `RenderingContextFactory`) rather than
 * a bare `TYPO3Fluid\Fluid\View\TemplateView` - a bare TemplateView never gets a template cache at
 * all (see BenchmarkRunCommand), so it can't be used to compare cold vs. warm rendering. This
 * mirrors the exact pattern packages/fluid-primitives/tests/Functional/FunctionalTestCase.php
 * already uses to render primitives with a realistic frontend-like request.
 */
final readonly class BenchmarkViewFactory
{
    public function __construct(
        private ViewFactoryInterface $viewFactory,
    ) {}

    public function createView(string $variantFolder, ServerRequestInterface $request): ViewInterface
    {
        $view = $this->viewFactory->create(new ViewFactoryData(templateRootPaths: [
            ExtensionManagementUtility::extPath('benchmarks', 'Resources/Private/Scenarios/' . $variantFolder),
        ], request: $request));

        // ViewFactoryInterface::create() only declares the generic core ViewInterface, which has no
        // getRenderingContext() of its own - only the concrete (@internal) FluidViewAdapter that
        // TYPO3\CMS\Fluid\View\FluidViewFactory always returns does. Same reliance as
        // packages/fluid-primitives/tests/Functional/FunctionalTestCase.php, which isn't flagged
        // because mago.toml excludes packages/fluid-primitives/tests/** from analysis.
        // @mago-expect analysis:non-existent-method
        // @mago-expect analysis:mixed-method-access
        // @mago-expect analysis:mixed-assignment
        $resolver = $view->getRenderingContext()->getViewHelperResolver();
        // @mago-expect analysis:mixed-method-access
        $resolver->addNamespace('primitives', new ComponentPrimitivesCollection());
        // @mago-expect analysis:mixed-method-access
        $resolver->addNamespace('bench', new BenchComponentCollection());
        // @mago-expect analysis:mixed-method-access
        $resolver->addNamespace('vanilla', new VanillaComponentCollection());
        // @mago-expect analysis:mixed-method-access
        $resolver->addNamespace('ui', 'Jramke\\FluidPrimitives\\ViewHelpers');
        // @mago-expect analysis:mixed-method-access
        $resolver->addNamespace('vite', 'Praetorius\\ViteAssetCollector\\ViewHelpers');

        return $view;
    }

    /**
     * A minimal but realistic frontend-like request (site + language + applicationType), so
     * translator/locale-dependent contexts (Input, Select) behave the same as they would on a
     * real page. Reuses `$GLOBALS['TYPO3_REQUEST']` if one is already set (e.g. inside the
     * BenchmarkMiddleware, which runs within a real request) instead of replacing it.
     */
    public function createFrontendRequest(): ServerRequestInterface
    {
        // This factory has no request-scoped construction path of its own (a console command has
        // no request at all yet), so $GLOBALS is the only way to reach/seed the one TYPO3 itself
        // uses - the same pattern packages/fluid-primitives/Classes/Registry/HydrationRegistry.php
        // already relies on for the same reason.
        // @mago-expect lint:no-global
        // @mago-expect analysis:mixed-assignment
        $request = $GLOBALS['TYPO3_REQUEST'] ?? new ServerRequest();
        if (!$request instanceof ServerRequest) {
            $request = new ServerRequest();
        }

        if (!$request->getAttribute('site')) {
            $baseUri = new Uri('https://benchmarks.example.com/');
            $site = new Site('benchmarks', 1, [
                'base' => (string)$baseUri,
                'languages' => [],
                'errorHandling' => [],
                'routes' => [],
            ]);
            $request = $request->withAttribute('site', $site);
        }

        if (!$request->getAttribute('language')) {
            $baseUri = new Uri('https://benchmarks.example.com/');
            $language = new SiteLanguage(0, 'en_US', $baseUri, ['title' => 'English']);
            $request = $request->withAttribute('language', $language);
        }

        $request = $request->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)->withAttribute(
            'normalizedParams',
            NormalizedParams::createFromRequest($request),
        );

        // @mago-expect lint:no-global
        $GLOBALS['TYPO3_REQUEST'] = $request;

        return $request;
    }
}
