<?php

declare(strict_types=1);

namespace FluidPrimitives\Demos\DataProcessing;

use FluidPrimitives\Demos\Service\DemoPages;
use Jramke\FluidPrimitives\Utility\Typed;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Frontend\Page\PageInformation;

/**
 * The sidebar, the demo cards of the overview and the GitHub link of a demo page.
 */
#[Autoconfigure(public: true)]
final readonly class DemoPageProcessor implements DataProcessorInterface
{
    private const string GITHUB_URL = 'https://github.com/jramke/fluid-primitives.com/tree/main/packages/demos';

    public function __construct(
        private DemoPages $demoPages,
    ) {}

    /**
     * @param array<array-key, mixed> $contentObjectConfiguration
     * @param array<array-key, mixed> $processorConfiguration
     * @param array<array-key, mixed> $processedData
     * @return array<array-key, mixed>
     */
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData,
    ): array {
        $request = $cObj->getRequest();
        // @mago-expect analysis:mixed-assignment
        $pageInformation = $request->getAttribute('frontend.page.information');
        $overview = $this->demoPages->overview($request);
        if (!$pageInformation instanceof PageInformation || $overview === null) {
            return $processedData;
        }

        $overviewUid = Typed::int($overview['uid'] ?? null);
        $demos = $this->demoPages->demos($overviewUid);
        $page = $pageInformation->getPageRecord();
        $slug = Typed::string($page['slug'] ?? null);
        $isOverview = Typed::int($page['uid'] ?? null) === $overviewUid;

        $processedData['demo'] = [
            'nav' => [
                [
                    'title' => 'Demos',
                    'items' => [
                        ['slug' => Typed::string($overview['slug'] ?? null), 'title' => 'Overview', 'preview' => false],
                        ...$demos,
                    ],
                ],
            ],
            'path' => $slug,
            'isOverview' => $isOverview,
            'demos' => $demos,
            'githubUrl' => $isOverview ? self::GITHUB_URL : self::GITHUB_URL . '#' . basename($slug),
        ];

        return $processedData;
    }
}
