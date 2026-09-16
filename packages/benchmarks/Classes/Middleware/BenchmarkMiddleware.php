<?php

declare(strict_types=1);

namespace FluidPrimitives\Benchmarks\Middleware;

use FluidPrimitives\Benchmarks\Benchmark\BenchmarkRunner;
use FluidPrimitives\Benchmarks\Benchmark\ScenarioRegistry;
use Jramke\FluidPrimitives\Utility\Typed;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Page\PageRenderer;

/**
 * Serves the benchmark scenarios in-browser at /benchmarks, entirely outside the normal
 * page-tree/TypoScript/content-element pipeline (no DB page, no site config changes needed) -
 * this is an internal dev tool, not a page real visitors ever see, so a plain middleware (the
 * same pattern packages/docs/Classes/Middleware/RegistryMiddleware.php already uses for a
 * similar "compute a response outside the page pipeline" need) is a better fit than an Extbase
 * plugin + routeEnhancer + backend page. Still runs inside the full request/DI lifecycle, so
 * rendering goes through the real ComponentRenderer/HydrationRegistry/AssetCollector - only the
 * page-tree/TypoScript machinery is bypassed.
 */
final readonly class BenchmarkMiddleware implements MiddlewareInterface
{
    private const string PATH_PREFIX = '/benchmarks';

    public function __construct(
        private BenchmarkRunner $runner,
        private ScenarioRegistry $scenarioRegistry,
        private PageRenderer $pageRenderer,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = rtrim($request->getUri()->getPath(), characters: '/');

        if (!str_starts_with($path, self::PATH_PREFIX)) {
            return $handler->handle($request);
        }

        if ($path === self::PATH_PREFIX) {
            return $this->renderIndex($request);
        }

        // explode() always returns at least one element, so $parts[0] is always defined - only a
        // second path segment (the variant) is optional.
        $parts = explode('/', trim(substr($path, strlen(self::PATH_PREFIX)), characters: '/'));
        $component = $parts[0];
        $variant = $parts[1] ?? null;

        if (
            !is_string($variant) ||
            !$this->scenarioRegistry->isValidComponent($component) ||
            !$this->scenarioRegistry->isValidVariant($variant)
        ) {
            return new HtmlResponse('Not Found', 404);
        }

        return $this->renderShow($request, $component, $variant);
    }

    private function renderIndex(ServerRequestInterface $request): ResponseInterface
    {
        $links = [];
        foreach ($this->scenarioRegistry->components() as $component) {
            foreach ($this->scenarioRegistry->variants() as $variant) {
                foreach ($this->scenarioRegistry->defaultInstanceCounts() as $count) {
                    $href = sprintf('%s/%s/%s?instances=%d', self::PATH_PREFIX, $component, $variant, $count);
                    $links[] = sprintf(
                        '<li><a href="%s">%s / %s / %d instances</a></li>',
                        htmlspecialchars($href),
                        htmlspecialchars($component),
                        htmlspecialchars($variant),
                        $count,
                    );
                }
            }
        }

        $body = '<h1>Fluid Primitives Benchmarks</h1><ul>' . implode('', $links) . '</ul>';

        $this->pageRenderer->setTitle('Fluid Primitives Benchmarks');
        $this->pageRenderer->setBodyContent($body);

        return new HtmlResponse($this->pageRenderer->render($request));
    }

    private function renderShow(ServerRequestInterface $request, string $component, string $variant): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $count = max(0, Typed::int($queryParams['instances'] ?? 10));

        $result = $this->runner->renderOnce($component, $variant, $count, $request);

        $backLink = sprintf('<p><a href="%s">&laquo; back to index</a></p>', self::PATH_PREFIX);
        $meta = sprintf(
            '<p>%s / %s / %d instances &mdash; server render took %.3fms</p>',
            htmlspecialchars($component),
            htmlspecialchars($variant),
            $count,
            $result->elapsedMs,
        );
        $resultNode = '<div id="bench-result" data-duration=""></div>';

        $body = $backLink . $meta . $resultNode . '<div id="bench-scenario">' . $result->html . '</div>';

        $this->pageRenderer->setTitle(sprintf('%s / %s - Fluid Primitives Benchmarks', $component, $variant));
        $this->pageRenderer->setBodyContent($body);

        return new HtmlResponse($this->pageRenderer->render($request));
    }
}
