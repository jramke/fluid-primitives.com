<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Services;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Core\Http\RequestFactory;

#[Autoconfigure(public: true)]
class UmamiService
{
    private const UMAMI_HOST = 'https://umami.joostramke.com';
    private const WEBSITE_ID = '16212a52-92f6-4b33-aec6-d31f5ad0314d';

    public function __construct(
        private readonly RequestFactory $requestFactory
    ) {}

    public function renderScriptTag(): string
    {
        return '<script defer src="' . self::UMAMI_HOST . '/script.js" data-website-id="' . self::WEBSITE_ID . '"></script>';
    }

    public function trackEvent(
        ServerRequestInterface $request,
        string $eventName,
        array $eventData = [],
    ): void {
        try {
            $url = (string) $request->getUri();
            $hostname = $request->getUri()->getHost();
            $referrer = $request->getHeaderLine('Referer');
            $userAgent = $request->getHeaderLine('User-Agent');

            $language = $request->getAttribute('language');
            $locale = (string)$language->getLocale() ?? 'en-US';

            $screen = '0x0';

            $payload = [
                'type' => 'event',
                'payload' => [
                    'hostname' => $hostname,
                    'language' => $locale,
                    'referrer' => $referrer,
                    'screen' => $screen,
                    'title' => $eventName,
                    'url' => $url,
                    'website' => self::WEBSITE_ID,
                    'name' => $eventName,
                    'data' => $eventData,
                ],
            ];

            $headers = [
                'Content-Type' => 'application/json',
                'User-Agent' => $userAgent,
            ];

            $this->requestFactory->request(
                self::UMAMI_HOST . '/api/send',
                'POST',
                [
                    'headers' => $headers,
                    'body' => json_encode($payload),
                ]
            );
        } catch (\Exception $e) {
        }
    }
}
