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
 * Creates the demo pages under the root page of the site, with their content elements and records. A page is
 * created when no page has its slug, so a new demo reaches an installation that has the older ones, production
 * included. A page that exists stays as it is.
 */
// One run that creates each demo with its content, kept in one place on purpose
// @mago-expect lint:kan-defect
#[AsCommand('demos:setup', 'Creates the demo pages and their records that are missing.')]
final class SetupCommand extends Command
{
    private const string OVERVIEW = '/demos';

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
        $this->initializeDataHandler();

        $overview = $this->page(self::OVERVIEW, $this->rootPageId(), [
            'title' => 'Demos',
            'abstract' => 'Common parts of a TYPO3 site, built with Fluid Primitives and the extensions you know.',
            'backend_layout' => 'pagets__Demo',
            'backend_layout_next_level' => 'pagets__Demo',
        ]);
        $data = $this->page(self::OVERVIEW . '/demo-data', $overview['uid'], [
            'title' => 'Demo data',
            'doktype' => 254,
            'nav_hide' => 1,
        ]);
        $dataPid = $data['uid'];
        if ($data['created']) {
            $this->createRecords($dataPid);
        }

        // Each page is placed after the one before it: a negative pid in DataHandler
        $search = $this->page(self::OVERVIEW . '/search', $overview['uid'], [
            'title' => 'Search',
            'abstract' => 'ke_search with suggestions while you type, in a Combobox, and paginated results that load without a reload.',
        ]);
        $news = $this->page(self::OVERVIEW . '/news', -$search['uid'], [
            'title' => 'News list',
            'abstract' => 'An EXT:news list with a search, a category and a date filter, and a pagination that loads the pages without a reload.',
        ]);
        $article = $this->page(self::OVERVIEW . '/news/article', -$news['uid'], [
            'title' => 'News article',
            'nav_hide' => 1,
        ]);
        $login = $this->page(self::OVERVIEW . '/login', -$article['uid'], [
            'title' => 'Login and registration',
            'abstract' => 'The login of felogin and a registration that shows the validation errors of Extbase on its fields, with the Form and the Password Input.',
        ]);
        $header = $this->page(self::OVERVIEW . '/header', -$login['uid'], [
            'title' => 'Site header',
            'abstract' => 'A Navigation Menu built from the page tree, a Dialog for the navigation on small screens and a Menu for the logged-in user.',
        ]);
        $account = $this->page(self::OVERVIEW . '/account', -$header['uid'], [
            'title' => 'Account settings',
            'abstract' => 'The settings page of the user menu in the site header.',
            'nav_hide' => 1,
        ]);

        if ($search['created']) {
            $this->createSearchContent($search['uid'], $dataPid, $article['uid']);
        }
        if ($news['created']) {
            $this->createContent($news['uid'], 'news_newssearchresult', [
                'pi_flexform' => $this->flexForm([
                    'sDEF' => ['settings.startingpoint' => $dataPid],
                    'additional' => ['settings.detailPid' => $article['uid']],
                ]),
            ]);
        }
        if ($article['created']) {
            $this->createContent($article['uid'], 'news_newsdetail', [
                'pi_flexform' => $this->flexForm([
                    'sDEF' => ['settings.startingpoint' => $dataPid],
                    'additional' => ['settings.backPid' => $news['uid']],
                ]),
            ]);
        }
        if ($login['created']) {
            $this->createLoginContent($login['uid'], $dataPid);
        }
        if ($header['created']) {
            $this->createContent($header['uid'], 'demos_siteheader');
        }
        if ($account['created']) {
            $siteHeader = $this->createContent($account['uid'], 'demos_siteheader');
            $this->createContent(-$siteHeader, 'demos_account');
        }

        $output->writeln('The demos are set up. Run ke_search:indexing to build the search index.');

        return Command::SUCCESS;
    }

    private function initializeDataHandler(): void
    {
        Bootstrap::initializeBackendAuthentication();
        // DataHandler reads both from the globals
        // @mago-expect lint:no-global(2)
        // @mago-expect analysis:mixed-assignment
        $backendUser = $GLOBALS['BE_USER'] ?? null;
        $GLOBALS['LANG'] = $this->languageServiceFactory->createFromUserPreferences(
            $backendUser instanceof BackendUserAuthentication ? $backendUser : null,
        );
    }

    private function rootPageId(): int
    {
        return array_values($this->siteFinder->getAllSites())[0]->getRootPageId();
    }

    /**
     * The page with the slug, created with the fields when there is none.
     *
     * @param int $pid The parent page, or the negative uid of the page to place it after
     * @param array<string, int|string> $fields
     * @return array{uid: int, created: bool}
     */
    private function page(string $slug, int $pid, array $fields): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('pages');
        $uid = (int)$queryBuilder
            ->select('uid')
            ->from('pages')
            ->where($queryBuilder->expr()->eq('slug', $queryBuilder->createNamedParameter($slug)))
            ->executeQuery()
            ->fetchOne();
        if ($uid > 0) {
            return ['uid' => $uid, 'created' => false];
        }

        // New pages are hidden by default
        $uids = $this->create(['pages' => ['NEW_page' => ['pid' => $pid, 'slug' => $slug, 'hidden' => 0, ...$fields]]]);

        return ['uid' => $uids['NEW_page'], 'created' => true];
    }

    /**
     * @param int $pid The page, or the negative uid of the content element to place it after
     * @param array<string, mixed> $fields
     */
    private function createContent(int $pid, string $contentType, array $fields = []): int
    {
        $uids = $this->create([
            'tt_content' => ['NEW_content' => [
                'pid' => $pid,
                'CType' => $contentType,
                'header_layout' => 100,
                ...$fields,
            ]],
        ]);

        return $uids['NEW_content'];
    }

    private function createSearchContent(int $pageUid, int $dataPid, int $articleUid): void
    {
        $searchBox = $this->createContent($pageUid, 'ke_search_pi1', [
            'pages' => $dataPid,
            'pi_flexform' => $this->flexForm([
                'MAIN' => ['resultPage' => $pageUid],
                'view' => ['showDate' => 1, 'highlightSword' => 1, 'previewMode' => 'abstract'],
                'pagebrowser' => ['resultsPerPage' => 5, 'pagebrowserOnTop' => 0, 'pagebrowserAtBottom' => 1],
            ]),
        ]);
        $this->createContent(-$searchBox, 'ke_search_pi2', [
            'pi_flexform' => $this->flexForm(['MAIN' => ['loadFlexformsFromOtherCE' => $searchBox]]),
        ]);

        $this->create([
            'tx_kesearch_indexerconfig' => ['NEW_indexer' => [
                'pid' => $dataPid,
                'title' => 'Demo news',
                'type' => 'news',
                'storagepid' => $dataPid,
                'sysfolder' => $dataPid,
                'targetpid' => $articleUid,
            ]],
        ]);
    }

    private function createLoginContent(int $pageUid, int $dataPid): void
    {
        $login = $this->createContent($pageUid, 'felogin_login', [
            'pi_flexform' => $this->flexForm([
                'sDEF' => [
                    'settings.pages' => $dataPid,
                    // @mago-expect lint:no-literal-password
                    'settings.showForgotPassword' => 0,
                    'settings.showLogoutFormAfterLogin' => 1,
                ],
            ]),
        ]);
        $this->createContent(-$login, 'demos_registration', [
            'pages' => $dataPid,
            // Hide at login
            'fe_group' => -1,
        ]);
    }

    /**
     * The news with their categories, and the user group of the registration.
     */
    private function createRecords(int $pid): void
    {
        /** @var array{categories: list<string>, articles: list<array{title: string, date: string|int, categories: list<string>, teaser: string, body: string}>} $data */
        $data = Yaml::parseFile(GeneralUtility::getFileAbsFileName('EXT:demos/Resources/Private/DemoData/News.yaml'));

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

        $this->create([
            'tx_news_domain_model_news' => $news,
            'fe_groups' => ['NEW_group' => ['pid' => $pid, 'title' => 'Demo members']],
        ]);
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
