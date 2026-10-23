<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Tests\Unit\Middleware;

use FluidPrimitives\Docs\Middleware\DocsMarkdownModeMiddleware;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\ResponseFactory;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class DocsMarkdownModeMiddlewareTest extends UnitTestCase
{
    private ?ServerRequestInterface $handled = null;

    private function process(ServerRequestInterface $request): ResponseInterface
    {
        $this->handled = null;
        $handler = new readonly class($this) implements RequestHandlerInterface {
            public function __construct(
                private DocsMarkdownModeMiddlewareTest $test,
            ) {}

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $this->test->record($request);

                return (new ResponseFactory())->createResponse(200);
            }
        };

        return (new DocsMarkdownModeMiddleware(new ResponseFactory()))->process($request, $handler);
    }

    public function record(ServerRequestInterface $request): void
    {
        $this->handled = $request;
    }

    #[Test]
    public function turnsADotMdUrlIntoTheMarkdownVariantOfThePage(): void
    {
        $response = $this->process(new ServerRequest('https://example.com/docs/introduction.md', 'GET'));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('/docs/introduction', $this->handled?->getUri()->getPath());
        $this->assertSame('9576', $this->handled?->getQueryParams()['type']);
        $this->assertTrue($this->handled?->getAttribute('docsMarkdownMode'));
    }

    #[Test]
    public function refusesTheMarkdownTypeOnAPageThatIsNotAMarkdownUrl(): void
    {
        $request = (new ServerRequest('https://example.com/docs/introduction', 'GET'))->withQueryParams([
            'type' => '9576',
        ]);

        $this->assertSame(404, $this->process($request)->getStatusCode());
        $this->assertNull($this->handled);

        // positive control: the same page with another type, or none, is handled
        $this->assertSame(200, $this->process($request->withQueryParams(['type' => '0']))->getStatusCode());
        $this->assertSame(
            200,
            $this->process(new ServerRequest('https://example.com/docs/introduction', 'GET'))->getStatusCode(),
        );
    }

    #[Test]
    public function leavesTheRegistryUrlsToTheRegistryMiddleware(): void
    {
        $request = new ServerRequest('https://example.com/registry/components/button/files/Button.entry.ts', 'GET');

        $this->assertSame(200, $this->process($request)->getStatusCode());
        $this->assertSame('/registry/components/button/files/Button.entry.ts', $this->handled?->getUri()->getPath());
    }
}
