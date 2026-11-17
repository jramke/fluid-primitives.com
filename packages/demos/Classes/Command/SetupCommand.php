<?php

declare(strict_types=1);

namespace FluidPrimitives\Demos\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Creates the demo pages, their content elements and records under the root page of the site, once. The demos
 * live in the database like on any TYPO3 site, so this command gives every installation, production included,
 * the same ones.
 */
#[AsCommand('demos:setup', 'Creates the pages and records of the demos, when they are missing.')]
final class SetupCommand extends Command
{
    private const string OVERVIEW_SLUG = '/demos';

    public function __construct(
        private readonly ConnectionPool $connectionPool,
        private readonly SiteFinder $siteFinder,
        private readonly LanguageServiceFactory $languageServiceFactory,
    ) {
        parent::__construct();
    }

    // @mago-expect lint:halstead
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->overviewExists()) {
            $output->writeln('The demos exist already.');
            return Command::SUCCESS;
        }

        Bootstrap::initializeBackendAuthentication();
        // DataHandler reads both from the globals
        // @mago-expect lint:no-global(2)
        // @mago-expect analysis:mixed-assignment
        $backendUser = $GLOBALS['BE_USER'] ?? null;
        $GLOBALS['LANG'] = $this->languageServiceFactory->createFromUserPreferences(
            $backendUser instanceof BackendUserAuthentication ? $backendUser : null,
        );

        // Read before anything is created: an error in the file must not leave half of the demos behind
        /** @var array{categories: list<string>, articles: list<array{title: string, date: string|int, categories: list<string>, teaser: string, body: string}>} $newsData */
        $newsData = Yaml::parseFile(GeneralUtility::getFileAbsFileName(
            'EXT:demos/Resources/Private/DemoData/News.yaml',
        ));

        $rootPageId = array_values($this->siteFinder->getAllSites())[0]->getRootPageId();

        // A new page is placed first among its siblings, so they are listed last to first. New pages are hidden by
        // default.
        $pages = $this->create([
            'pages' => array_map(static fn(array $page): array => ['hidden' => 0, ...$page], [
                'NEW_overview' => [
                    'pid' => $rootPageId,
                    'title' => 'Demos',
                    'slug' => self::OVERVIEW_SLUG,
                    'abstract' => 'Common parts of a TYPO3 site, built with Fluid Primitives and the extensions you know.',
                    'backend_layout' => 'pagets__Demo',
                    'backend_layout_next_level' => 'pagets__Demo',
                ],
                'NEW_data' => [
                    'pid' => 'NEW_overview',
                    'title' => 'Demo data',
                    'doktype' => 254,
                    'nav_hide' => 1,
                ],
                'NEW_login' => [
                    'pid' => 'NEW_overview',
                    'title' => 'Login and registration',
                    'slug' => self::OVERVIEW_SLUG . '/login',
                    'abstract' => 'The login of felogin and a registration that shows the validation errors of Extbase on its fields, with the Form and the Password Input.',
                ],
                'NEW_article' => [
                    'pid' => 'NEW_overview',
                    'title' => 'News article',
                    'slug' => self::OVERVIEW_SLUG . '/news/article',
                    'nav_hide' => 1,
                ],
                'NEW_news' => [
                    'pid' => 'NEW_overview',
                    'title' => 'News list',
                    'slug' => self::OVERVIEW_SLUG . '/news',
                    'abstract' => 'An EXT:news list with a search, a category and a date filter, and a pagination that loads the pages without a reload.',
                ],
                'NEW_search' => [
                    'pid' => 'NEW_overview',
                    'title' => 'Search',
                    'slug' => self::OVERVIEW_SLUG . '/search',
                    'abstract' => 'ke_search with suggestions while you type, in a Combobox, and paginated results that load without a reload.',
                ],
            ]),
        ]);
        $dataPid = $pages['NEW_data'];

        $records = $this->create([
            ...$this->newsRecords($newsData, $dataPid),
            'fe_groups' => ['NEW_group' => ['pid' => $dataPid, 'title' => 'Demo members']],
            'tx_kesearch_indexerconfig' => ['NEW_indexer' => [
                'pid' => $dataPid,
                'title' => 'Demo news',
                'type' => 'news',
                'storagepid' => $dataPid,
                'sysfolder' => $dataPid,
                'targetpid' => $pages['NEW_article'],
            ]],
            'tt_content' => [
                'NEW_searchbox' => [
                    'pid' => $pages['NEW_search'],
                    'CType' => 'ke_search_pi1',
                    'header' => 'Search',
                    'header_layout' => 100,
                    'pages' => $dataPid,
                    'pi_flexform' => $this->flexForm([
                        'MAIN' => ['resultPage' => $pages['NEW_search']],
                        'view' => ['showDate' => 1, 'highlightSword' => 1, 'previewMode' => 'abstract'],
                        'pagebrowser' => ['resultsPerPage' => 5, 'pagebrowserOnTop' => 0, 'pagebrowserAtBottom' => 1],
                    ]),
                ],
                'NEW_newslist' => [
                    'pid' => $pages['NEW_news'],
                    'CType' => 'news_newssearchresult',
                    'header' => 'News',
                    'header_layout' => 100,
                    'pi_flexform' => $this->flexForm([
                        'sDEF' => ['settings.startingpoint' => $dataPid],
                        'additional' => ['settings.detailPid' => $pages['NEW_article']],
                    ]),
                ],
                'NEW_newsdetail' => [
                    'pid' => $pages['NEW_article'],
                    'CType' => 'news_newsdetail',
                    'header' => 'News article',
                    'header_layout' => 100,
                    'pi_flexform' => $this->flexForm([
                        'sDEF' => ['settings.startingpoint' => $dataPid],
                        'additional' => ['settings.backPid' => $pages['NEW_news']],
                    ]),
                ],
                'NEW_registration' => [
                    'pid' => $pages['NEW_login'],
                    'CType' => 'demos_registration',
                    'header' => 'Registration',
                    'header_layout' => 100,
                    'pages' => $dataPid,
                    // Hide at login
                    'fe_group' => -1,
                ],
                'NEW_login' => [
                    'pid' => $pages['NEW_login'],
                    'CType' => 'felogin_login',
                    'header' => 'Login',
                    'header_layout' => 100,
                    'pi_flexform' => $this->flexForm([
                        'sDEF' => [
                            'settings.pages' => $dataPid,
                            // @mago-expect lint:no-literal-password
                            'settings.showForgotPassword' => 0,
                            'settings.showLogoutFormAfterLogin' => 1,
                        ],
                    ]),
                ],
            ],
        ]);

        $this->create([
            'tt_content' => ['NEW_results' => [
                // A negative pid places the element after the search box
                'pid' => -$records['NEW_searchbox'],
                'CType' => 'ke_search_pi2',
                'header' => 'Search results',
                'header_layout' => 100,
                'pi_flexform' => $this->flexForm(['MAIN' => ['loadFlexformsFromOtherCE' => $records['NEW_searchbox']]]),
            ]],
        ]);

        $output->writeln('Created the demos. Run ke_search:indexing to build the search index.');

        return Command::SUCCESS;
    }

    private function overviewExists(): bool
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('pages');

        return (
            (int)$queryBuilder
                ->count('uid')
                ->from('pages')
                ->where($queryBuilder->expr()->eq('slug', $queryBuilder->createNamedParameter(self::OVERVIEW_SLUG)))
                ->executeQuery()
                ->fetchOne() > 0
        );
    }

    /**
     * Creates the categories, and returns the articles that use them.
     *
     * @param array{categories: list<string>, articles: list<array{title: string, date: string|int, categories: list<string>, teaser: string, body: string}>} $data
     * @return array<string, array<string, array<string, mixed>>>
     */
    private function newsRecords(array $data, int $pid): array
    {
        // In their own run: DataHandler leaves the categories of a news record empty when they are new in the same one
        $categories = [];
        foreach ($data['categories'] as $index => $title) {
            $categories['NEW_category' . $index] = ['pid' => $pid, 'title' => $title];
        }
        $created = $this->create(['sys_category' => $categories]);
        $categoryIds = [];
        foreach ($data['categories'] as $index => $title) {
            $categoryIds[$title] = $created['NEW_category' . $index];
        }

        $news = [];
        foreach ($data['articles'] as $index => $article) {
            $news['NEW_news' . $index] = [
                'pid' => $pid,
                'title' => $article['title'],
                'teaser' => $article['teaser'],
                'bodytext' => trim($article['body']),
                'datetime' => is_int($article['date']) ? $article['date'] : (int)strtotime($article['date'] . ' 09:00'),
                'categories' => implode(',', array_map(
                    static fn(string $category): int => $categoryIds[$category],
                    $article['categories'],
                )),
            ];
        }

        return ['tx_news_domain_model_news' => $news];
    }

    /**
     * @param array<string, array<string, int|string>> $sheets
     * @return array{data: array<string, array{lDEF: array<string, array{vDEF: int|string}>}>}
     */
    private function flexForm(array $sheets): array
    {
        $data = [];
        foreach ($sheets as $sheet => $fields) {
            foreach ($fields as $field => $value) {
                $data[$sheet]['lDEF'][$field] = ['vDEF' => $value];
            }
        }

        return ['data' => $data];
    }

    /**
     * @param array<string, array<array-key, array<array-key, mixed>>> $dataMap
     * @return array<array-key, int> The uids of the new records by their NEW id
     */
    private function create(array $dataMap): array
    {
        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start($dataMap, []);
        $dataHandler->process_datamap();

        if ($dataHandler->errorLog !== []) {
            throw new \RuntimeException(implode("\n", $dataHandler->errorLog), 1763337700);
        }

        return array_map(intval(...), $dataHandler->substNEWwithIDs);
    }
}
