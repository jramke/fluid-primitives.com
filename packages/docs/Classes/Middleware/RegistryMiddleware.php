<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Middleware;

use FluidPrimitives\Docs\Registry\ComponentRegistry;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Http\Response;

final class RegistryMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly ComponentRegistry $componentRegistry) {}

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $path = $request->getUri()->getPath();

        if (!str_starts_with($path, '/registry/')) {
            return $handler->handle($request);
        }

        // /registry/components
        if ($path === '/registry/components') {
            return $this->listComponents($this->componentRegistry);
        }

        $parts = explode('/', trim($path, '/'));


        $component = $parts[2] ?? null;
        if (!$component || !$this->componentRegistry->has($component)) {
            return new JsonResponse(['error' => 'Not Found'], 404);
        }

        // /registry/components/{component}
        if (!str_contains($path, '/files/')) {
            return $this->componentManifest($this->componentRegistry, $component);
        }

        // /registry/components/{component}/files/{file}
        $file = ($parts[3] ?? null) === 'files' ? ($parts[4] ?? null) : null;
        if (!$file) {
            return new JsonResponse(['error' => 'Not Found'], 404);
        }

        return $this->serveFile($this->componentRegistry, $component, $file);
    }

    private function listComponents(ComponentRegistry $registry): ResponseInterface
    {
        $list = array_map(
            fn($c) => [
                'key' => $c->key,
                'name' => $c->name,
                'description' => $c->meta['description'] ?? '',
                'files' => $c->files,
            ],
            $registry->list()
        );

        return new JsonResponse($list, 200, $this->headers());
    }

    private function componentManifest(
        ComponentRegistry $registry,
        string $component
    ): ResponseInterface {
        $c = $registry->get($component);

        return new JsonResponse(
            [
                'key' => $c->key,
                'name' => $c->name,
                'description' => $c->meta['description'] ?? '',
                'files' => $c->files,
            ],
            200,
            $this->headers()
        );
    }

    private function serveFile(
        ComponentRegistry $registry,
        string $component,
        string $file
    ): ResponseInterface {
        $c = $registry->get($component);

        if (!in_array($file, $c->files, true)) {
            return new Response('Not Found', 404);
        }

        $path = $c->basePath . $file;
        $stream = fopen($path, 'rb');

        return new Response(
            $stream,
            200,
            array_merge(
                $this->headers(),
                [
                    'Content-Type' => 'text/plain; charset=utf-8',
                ]
            )
        );
    }

    private function headers(): array
    {
        return [
            'Cache-Control' => 'public, max-age=3600',
        ];
    }
}
