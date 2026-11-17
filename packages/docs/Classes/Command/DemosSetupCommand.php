<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Command;

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
 * Creates the demos: the imaginary site "Acme" of EXT:demos with its records, and a docs page per demo that shows
 * a page of Acme in a frame. A page is created when no page has its slug, so a new demo reaches an installation
 * that has the older ones, production included. A page that exists stays as it is.
 */
// One run that creates each demo with its content, kept in one place on purpose
// @mago-expect lint:kan-defect
#[AsCommand('demos:setup', 'Creates the demo pages and their records that are missing.')]
final class DemosSetupCommand extends Command
{
    private const string OVERVIEW = '/demos';
    private const string SITE = '/acme';

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
        $rootPageId = $this->rootPageId();

        // The imaginary site, each page placed after the one before it: a negative pid in DataHandler
        $site = $this->page(self::SITE, $rootPageId, [
            'title' => 'Acme',
            'nav_hide' => 1,
            'backend_layout' => 'pagets__Acme',
            'backend_layout_next_level' => 'pagets__Acme',
        ]);
        $data = $this->page(self::SITE . '/data', $site['uid'], ['title' => 'Data', 'doktype' => 254]);
        $dataPid = $data['uid'];
        $news = $this->page(self::SITE . '/news', -$dataPid, ['title' => 'News']);
        $article = $this->page(self::SITE . '/news/article', $news['uid'], ['title' => 'Article', 'nav_hide' => 1]);
        $login = $this->page(self::SITE . '/login', -$news['uid'], ['title' => 'Log in']);
        $register = $this->page(self::SITE . '/register', -$login['uid'], ['title' => 'Create an account']);
        $account = $this->page(self::SITE . '/account', -$register['uid'], ['title' => 'Account settings']);

        if ($data['created']) {
            $this->createRecords($dataPid);
        }
        if ($site['created']) {
            $this->createContent($site['uid'], 'text', [
                'header' => 'Acme',
                'header_layout' => 1,
                'bodytext' => '<p>An imaginary site for the demos of Fluid Primitives.</p>',
            ]);
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
            $this->createContent($login['uid'], 'felogin_login', [
                'pi_flexform' => $this->flexForm([
                    'sDEF' => [
                        'settings.pages' => $dataPid,
                        // @mago-expect lint:no-literal-password
                        'settings.showForgotPassword' => 0,
                    ],
                    's_redirect' => [
                        'settings.redirectMode' => 'login',
                        'settings.redirectPageLogin' => $account['uid'],
                    ],
                ]),
            ]);
        }
        if ($register['created']) {
            // Hide at login
            $this->createContent($register['uid'], 'demos_registration', ['pages' => $dataPid, 'fe_group' => -1]);
        }
        if ($account['created']) {
            $this->createContent($account['uid'], 'demos_account');
        }

        // The docs pages, each shows a page of Acme in a frame
        $overview = $this->page(self::OVERVIEW, $rootPageId, [
            'title' => 'Demos',
            'abstract' => 'Parts of a TYPO3 site built with Fluid Primitives, next to EXT:news and felogin. Each demo is a page of an imaginary site, its code is in packages/demos.',
            'backend_layout' => 'pagets__Demo',
            'backend_layout_next_level' => 'pagets__Demo',
        ]);
        $previous = 0;
        foreach ([
            [
                'slug' => '/news',
                'frame' => $news['uid'],
                'title' => 'News list',
                'abstract' => 'An EXT:news list with a search, a category and a date filter. The list and its pages load without a reload.',
                'keywords' => 'Input, Select, Date Picker, Pagination',
            ],
            [
                'slug' => '/login',
                'frame' => $login['uid'],
                'title' => 'Login and registration',
                'abstract' => 'The felogin login, a registration that shows the validation errors of Extbase on its fields, and a user menu with account settings.',
                'keywords' => 'Form, Field, Input, Password Input, Button, Menu',
            ],
            [
                'slug' => '/header',
                'frame' => $site['uid'],
                'title' => 'Site header',
                'abstract' => 'A navigation with two dropdowns. On small screens it moves into a drawer with collapsible sections.',
                'keywords' => 'Navigation Menu, Dialog, Collapsible',
            ],
        ] as $demo) {
            $page = $this->page(self::OVERVIEW . $demo['slug'], $previous === 0 ? $overview['uid'] : -$previous, [
                'title' => $demo['title'],
                'abstract' => $demo['abstract'],
                'keywords' => $demo['keywords'],
            ]);
            if ($page['created']) {
                $this->createContent($page['uid'], 'docs_demoframe', [
                    'header' => $demo['title'],
                    'header_link' => 't3://page?uid=' . $demo['frame'],
                ]);
            }
            $previous = $page['uid'];
        }

        $output->writeln('The demos are set up.');

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

    /**
     * The news with their categories, and the user group of the registration.
     */
    private function createRecords(int $pid): void
    {
        /** @var array{categories: list<string>, articles: list<array{title: string, date: string|int, categories: list<string>, teaser: string, body: string}>} $data */
        $data = Yaml::parseFile(GeneralUtility::getFileAbsFileName('EXT:docs/Resources/Private/DemoData/News.yaml'));

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
