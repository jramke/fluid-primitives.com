<?php

declare(strict_types=1);

use FluidPrimitives\Docs\Utility\ConfigurationFromEnvironment;
use TYPO3\CMS\Core\Cache\Backend\NullBackend;
use TYPO3\CMS\Core\Core\Environment;

// settings.php holds no secret and no value of one machine. Those come from the environment, see the README:
//
// - A `TYPO3__SECTION__key` variable sets `$GLOBALS['TYPO3_CONF_VARS']['SECTION']['key']`, `__` separates the levels.
// - `APP_URL` is the base URL of the site (config/sites/main/config.yaml) and the only host the site answers to.
// - `DISABLE_CACHES=true` turns the caches off for development.
//
// A variable always wins over the defaults below. A .env file only fills a variable the process does not have.

$context = Environment::getContext();
$appHost = (string)parse_url((string)getenv('APP_URL'), PHP_URL_HOST);

$defaults = [];

// The host header has to be the host of APP_URL: Apache fills SERVER_NAME from that header
if ($appHost !== '') {
    $defaults = array_replace_recursive($defaults, [
        'SYS' => ['trustedHostsPattern' => '(?:' . preg_quote($appHost, delimiter: '/') . ')'],
    ]);
}

if ($context->isProduction()) {
    // Only a proxy of the private networks of a container setup is believed when it says which host, address and
    // scheme the visitor used. Set TYPO3__SYS__reverseProxyIP to the address of yours.
    $privateNetworks = '127.0.0.1,10.0.0.0/8,172.16.0.0/12,192.168.0.0/16';
    $defaults = array_replace_recursive($defaults, [
        'SYS' => [
            'reverseProxyIP' => $privateNetworks,
            'reverseProxySSL' => $privateNetworks,
            'reverseProxyHeaderMultiValue' => 'first',
        ],
    ]);
}

if ($context->isDevelopment()) {
    $defaults = array_replace_recursive($defaults, [
        'BE' => ['debug' => 1, 'sessionTimeout' => 36000],
        'FE' => ['debug' => 1],
        'SYS' => ['displayErrors' => 1, 'exceptionalErrors' => 12290, 'devIPmask' => '*'],
    ]);
}

if (getenv('DISABLE_CACHES') === 'true') {
    $nullCaches = ['l10n', 'hash', 'pages', 'typoscript', 'core', 'rootline', 'extbase', 'database_schema'];
    $defaults = array_replace_recursive($defaults, [
        'SYS' => [
            'caching' => [
                'cacheConfigurations' => array_fill_keys($nullCaches, ['backend' => NullBackend::class]),
            ],
        ],
    ]);
}

if (getenv('IS_DDEV_PROJECT') === 'true') {
    $defaults = array_replace_recursive($defaults, [
        // This GFX configuration allows processing by installed ImageMagick 6
        'GFX' => [
            'processor' => 'ImageMagick',
            'processor_path' => '/usr/bin/',
            'processor_path_lzw' => '/usr/bin/',
        ],
        // ddev serves the project under more than one host
        'SYS' => ['trustedHostsPattern' => '.*.*'],
    ]);
}

$mapper = new ConfigurationFromEnvironment();
$configuration = $mapper->apply(
    // @mago-expect lint:no-global
    // @mago-expect analysis:mixed-argument
    array_replace_recursive($GLOBALS['TYPO3_CONF_VARS'], $defaults),
    getenv(),
);

// An unset secret is an error here, not a TYPO3 that runs with the placeholder of settings.php
$missing = $mapper->missing($configuration, [
    'SYS/encryptionKey',
    'DB/Connections/Default/host',
    'DB/Connections/Default/dbname',
    'DB/Connections/Default/user',
    'DB/Connections/Default/password',
]);
if ($appHost === '') {
    $missing[] = 'APP_URL';
}
if ($missing !== []) {
    throw new \RuntimeException(
        sprintf(
            'These environment variables are not set: %s. Copy .env.default to .env for development, an image gets them from its host.',
            implode(', ', $missing),
        ),
        1_788_300_001,
    );
}

// @mago-expect lint:no-global
$GLOBALS['TYPO3_CONF_VARS'] = $configuration;
