<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Cache\Backend\NullBackend;
use TYPO3\CMS\Core\Core\Environment;

// The committed settings.php holds no secret and no debug option. A production image reads its secrets from the
// environment and refuses to start without them, every other context gets the development values below.

if (Environment::getContext()->isProduction()) {
    $required = static function (string $name): string {
        $value = getenv($name);
        if (!is_string($value) || $value === '') {
            throw new \RuntimeException(
                sprintf('The environment variable %s has to be set in the production context.', $name),
                1_788_300_001,
            );
        }

        return $value;
    };

    // @mago-expect lint:no-global(2)
    // @mago-expect analysis:mixed-argument
    $GLOBALS['TYPO3_CONF_VARS'] = array_replace_recursive($GLOBALS['TYPO3_CONF_VARS'], [
        'SYS' => [
            'encryptionKey' => $required('TYPO3_ENCRYPTION_KEY'),
            // Only a proxy of the private networks of a container setup is believed when it says which host,
            // address and scheme the visitor used. Set PROXY_IP to the address of yours.
            'reverseProxySSL' => getenv('PROXY_IP') ?: '127.0.0.1,10.0.0.0/8,172.16.0.0/12,192.168.0.0/16',
            'reverseProxyIP' => getenv('PROXY_IP') ?: '127.0.0.1,10.0.0.0/8,172.16.0.0/12,192.168.0.0/16',
            'reverseProxyHeaderMultiValue' => 'first',
            // A pattern for the host header, not SERVER_NAME: Apache takes that from the same header.
            'trustedHostsPattern' =>
                '(?:' . (getenv('TRUSTED_HOSTS_PATTERN') ?: '(?:www\.)?fluid-primitives\.com') . ')',
        ],
        'DB' => [
            'Connections' => [
                'Default' => [
                    'host' => getenv('TYPO3_DB_HOST') ?: 'db',
                    'port' => getenv('TYPO3_DB_PORT') ?: '3306',
                    'user' => getenv('TYPO3_DB_USERNAME') ?: 'db',
                    'password' => $required('TYPO3_DB_PASSWORD'),
                    'dbname' => getenv('TYPO3_DB_NAME') ?: 'db',
                ],
            ],
        ],
    ]);

    // The Install Tool stays locked unless the password hash is given
    $installToolPassword = getenv('TYPO3_INSTALL_TOOL_PASSWORD');
    if (is_string($installToolPassword) && $installToolPassword !== '') {
        // @mago-expect lint:no-global
        // @mago-expect analysis:mixed-array-assignment(2)
        $GLOBALS['TYPO3_CONF_VARS']['BE']['installToolPassword'] = $installToolPassword;
    }
}

if (!Environment::getContext()->isProduction()) {
    // Not a secret: the key of a development installation, so a stray copy of it protects nothing
    // @mago-expect lint:no-global
    // @mago-expect analysis:mixed-array-assignment(2)
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] = getenv('TYPO3_ENCRYPTION_KEY') ?:
    'development-only-key-fluid-primitives-0f3a9c1d7b2e4a68c5d09e1f3b7a2c4d8e6f0a1b3c5d7e9f';
}

if (getenv('IS_DDEV_PROJECT') === 'true') {
    // @mago-expect lint:no-global(2)
    // @mago-expect analysis:mixed-argument
    $GLOBALS['TYPO3_CONF_VARS'] = array_replace_recursive($GLOBALS['TYPO3_CONF_VARS'], [
        'DB' => [
            'Connections' => [
                'Default' => [
                    'dbname' => 'db',
                    'driver' => 'mysqli',
                    'host' => 'db',
                    'password' => 'db', // @mago-expect lint:no-literal-password
                    'port' => '3306',
                    'user' => 'db',
                ],
            ],
        ],
        // This GFX configuration allows processing by installed ImageMagick 6
        'GFX' => [
            'processor' => 'ImageMagick',
            'processor_path' => '/usr/bin/',
            'processor_path_lzw' => '/usr/bin/',
        ],
        // This mail configuration sends all emails to mailpit
        'MAIL' => [
            'transport' => 'smtp',
            'transport_smtp_encrypt' => false,
            'transport_smtp_server' => 'localhost:1025',
        ],
        'SYS' => [
            'trustedHostsPattern' => '.*.*',
            'devIPmask' => '*',
            'displayErrors' => 1,
            'exceptionalErrors' => 12290,
            'caching' => [
                'cacheConfigurations' => [
                    'l10n' => [
                        'backend' => NullBackend::class,
                    ],
                    'hash' => [
                        'backend' => NullBackend::class,
                    ],
                    'pages' => [
                        'backend' => NullBackend::class,
                    ],
                    'typoscript' => [
                        'backend' => NullBackend::class,
                    ],
                    'core' => [
                        'backend' => NullBackend::class,
                    ],
                    'rootline' => [
                        'backend' => NullBackend::class,
                    ],
                    'extbase' => [
                        'backend' => NullBackend::class,
                    ],
                    'database_schema' => [
                        'backend' => NullBackend::class,
                    ],
                ],
            ],
        ],
        'BE' => [
            'sessionTimeout' => 36000,
            'debug' => 1,
        ],
        'FE' => [
            'debug' => 1,
        ],
    ]);
}
