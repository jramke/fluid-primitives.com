<?php

declare(strict_types=1);

namespace FluidPrimitives\Demos\EventListener;

use GeorgRinger\News\Domain\Model\Category;
use GeorgRinger\News\Domain\Model\Dto\NewsDemand;
use GeorgRinger\News\Domain\Model\Dto\Search;
use GeorgRinger\News\Domain\Repository\CategoryRepository;
use GeorgRinger\News\Event\NewsSearchResultActionEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;

/**
 * Gives the filters of the news search result what EXT:news does not assign: the categories of its storage
 * folder as `categoryItems`, the items of the category select, and the searched dates as `period`, the value of
 * the range date picker (`Y-m-d`, one date or two).
 */
#[AsEventListener]
final readonly class AddFiltersToNewsSearch
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

        $items = [];
        $storagePid = (int)$demand->getStoragePage();
        foreach ($this->categoryRepository->findParentCategoriesByPid($storagePid) as $category) {
            if (!$category instanceof Category) {
                continue;
            }
            $items[] = ['value' => (string)$category->getUid(), 'label' => $category->getTitle()];
        }

        $values['categoryItems'] = $items;
        $values['period'] = $this->period($values['search'] ?? null);
        $event->setAssignedValues($values);
    }

    /**
     * @return list<string>
     */
    private function period(mixed $search): array
    {
        if (!$search instanceof Search) {
            return [];
        }

        $period = [];
        foreach ([$search->getMinimumDate(), $search->getMaximumDate()] as $date) {
            $timestamp = strtotime($date);
            if ($timestamp === false) {
                break;
            }
            $period[] = date('Y-m-d', $timestamp);
        }

        return $period;
    }
}
