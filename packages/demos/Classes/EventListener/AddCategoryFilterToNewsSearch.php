<?php

declare(strict_types=1);

namespace FluidPrimitives\Demos\EventListener;

use GeorgRinger\News\Domain\Model\Category;
use GeorgRinger\News\Domain\Model\Dto\NewsDemand;
use GeorgRinger\News\Domain\Repository\CategoryRepository;
use GeorgRinger\News\Event\NewsSearchResultActionEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;

/**
 * Gives the news search result the categories of its storage folder as `categoryItems`, the items of the category
 * select. The search result of EXT:news has no category list of its own.
 */
#[AsEventListener]
final readonly class AddCategoryFilterToNewsSearch
{
    public function __construct(
        private CategoryRepository $categoryRepository,
    ) {}

    public function __invoke(NewsSearchResultActionEvent $event): void
    {
        $values = $event->getAssignedValues();
        // @mago-expect analysis:mixed-assignment
        $demand = $values['demand'] ?? null;
        if (!$demand instanceof NewsDemand) {
            return;
        }

        $items = [['value' => '', 'label' => 'All categories']];
        $storagePid = (int)$demand->getStoragePage();
        foreach ($this->categoryRepository->findParentCategoriesByPid($storagePid) as $category) {
            if (!$category instanceof Category) {
                continue;
            }
            $items[] = ['value' => (string)$category->getUid(), 'label' => $category->getTitle()];
        }

        $values['categoryItems'] = $items;
        $event->setAssignedValues($values);
    }
}
