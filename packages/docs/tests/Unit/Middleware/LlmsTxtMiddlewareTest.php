<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Tests\Unit\Middleware;

use FluidPrimitives\Docs\Middleware\LlmsTxtMiddleware;
use FluidPrimitives\Docs\Services\LlmsTxtBuilder;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\ResponseFactory;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Http\StreamFactory;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Site\Entity\NullSite;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class LlmsTxtMiddlewareTest extends UnitTestCase
{
    /** @var list<string> */
    private array $baseUrls = [];

    private function middleware(): LlmsTxtMiddleware
    {
        $this->baseUrls = [];
        $builder = $this->createStub(LlmsTxtBuilder::class);
        $builder
            ->method('buildForSite')
            ->willReturnCallback(function (string $baseUrl): string {
                $this->baseUrls[] = $baseUrl;

                return '# Fluid Primitives';
            });

        return new LlmsTxtMiddleware($builder, new StreamFactory(), new ResponseFactory());
    }

    private function handler(): RequestHandlerInterface
    {
        return new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return (new ResponseFactory())->createResponse(204);
            }
        };
    }

    #[Test]
    public function linksToTheBaseOfTheSiteWhateverHostTheRequestCarries(): void
    {
        $site = new Site('docs', 1, ['base' => 'https://fluid-primitives.com/']);
        $request = (new ServerRequest(
            'https://evil.example/llms.txt',
            'GET',
            null,
            [],
            ['HTTP_HOST' => 'evil.example'],
        ))
            ->withHeader('X-Forwarded-Host', 'evil.example')
            ->withAttribute('site', $site);

        $response = $this->middleware()->process($request, $this->handler());

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['https://fluid-primitives.com'], $this->baseUrls);
        $this->assertSame('# Fluid Primitives', (string)$response->getBody());
        $this->assertSame('nosniff', $response->getHeaderLine('X-Content-Type-Options'));
    }

    #[Test]
    public function answersNothingForAHostThatMatchesNoSiteOrAnotherMethod(): void
    {
        $unknownHost = (new ServerRequest('https://evil.example/llms.txt', 'GET'))->withAttribute(
            'site',
            new NullSite(),
        );
        $post = (new ServerRequest('https://fluid-primitives.com/llms.txt', 'POST'))->withAttribute('site', new Site(
            'docs',
            1,
            ['base' => 'https://fluid-primitives.com/'],
        ));

        $this->assertSame(404, $this->middleware()->process($unknownHost, $this->handler())->getStatusCode());
        $this->assertSame(404, $this->middleware()->process($post, $this->handler())->getStatusCode());
        $this->assertSame([], $this->baseUrls);
    }

    #[Test]
    public function leavesOtherUrlsToTheNextMiddleware(): void
    {
        $request = new ServerRequest(new Uri('https://fluid-primitives.com/docs'), 'GET');

        $this->assertSame(204, $this->middleware()->process($request, $this->handler())->getStatusCode());
    }
}
