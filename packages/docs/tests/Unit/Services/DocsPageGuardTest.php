<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Tests\Unit\Services;

use FluidPrimitives\Docs\Routing\Aspect\ValidatedPathMapper;
use FluidPrimitives\Docs\Services\DocsPageGuard;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Core\Http\ResponseFactory;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class DocsPageGuardTest extends UnitTestCase
{
    // the path mapper asks GeneralUtility for the cache manager
    protected bool $resetSingletonInstances = true;

    private function guard(): DocsPageGuard
    {
        $mapper = new class extends ValidatedPathMapper {
            protected function collectValidPaths(): array
            {
                return ['' => true, 'docs/introduction' => true, 'docs/components/button' => true];
            }
        };

        return new DocsPageGuard($mapper, new ResponseFactory());
    }

    #[Test]
    public function resolvesThePathOfAPageThatExists(): void
    {
        $request = new ServerRequest('https://example.com/docs/introduction', 'GET');

        $this->assertSame('docs/introduction', $this->guard()->resolvePath('docs/introduction', $request));
        $this->assertSame('docs/introduction', $this->guard()->resolvePath('docs/introduction/', $request));
        $this->assertSame('', $this->guard()->resolvePath('', $request));
    }

    /**
     * @return iterable<string, array{0: string}>
     */
    public static function pathsOfNoPage(): iterable
    {
        yield 'a file outside the content folder' => ['../../../../../CLAUDE'];
        yield 'a page that is not in the navigation' => ['docs/viewhelpers/fileUploadDeleteCheckbox'];
        yield 'an encoded parent folder' => ['docs/%2e%2e/introduction'];
        yield 'a backslash' => ['docs\\introduction'];
        yield 'a page of another case' => ['docs/Introduction.md'];
    }

    #[Test]
    #[DataProvider('pathsOfNoPage')]
    public function resolvesNothingForAPathThatIsNoPage(string $path): void
    {
        $request = new ServerRequest('https://example.com/docs/introduction', 'GET');

        $this->assertNull($this->guard()->resolvePath($path, $request));
    }

    #[Test]
    public function doesNotRenderAPageForAPostThatBringsAnotherPath(): void
    {
        $request = (new ServerRequest('https://example.com/docs/introduction', 'POST'))->withParsedBody([
            'tx_docs_docs' => ['path' => 'docs/components/button'],
        ]);

        try {
            $this->guard()->resolvePath('docs/components/button', $request);
            self::fail('A POST must not render a page.');
        } catch (PropagateResponseException $exception) {
            $this->assertSame(405, $exception->getResponse()->getStatusCode());
            $this->assertSame('GET, HEAD', $exception->getResponse()->getHeaderLine('Allow'));
        }

        // positive control: the same path is a page for a GET and a HEAD
        foreach (['GET', 'HEAD'] as $method) {
            $this->assertSame('docs/components/button', $this->guard()->resolvePath(
                'docs/components/button',
                new ServerRequest('https://example.com/docs/introduction', $method),
            ));
        }
    }
}
