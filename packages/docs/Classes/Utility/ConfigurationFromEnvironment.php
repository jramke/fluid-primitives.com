<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Utility;

use TYPO3\CMS\Core\Utility\ArrayUtility;

/**
 * Sets values of `$GLOBALS['TYPO3_CONF_VARS']` from environment variables, so `settings.php` can be committed
 * without a secret and a host can change a value without a deploy.
 *
 * `TYPO3__` starts the name and `__` separates the levels: `TYPO3__DB__Connections__Default__host` is
 * `$GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['host']`. Whole numbers, decimals, `true`, `false`
 * and `null` become that type, everything else stays a string. A path in {@see self::STRINGS} stays a string
 * whatever it looks like, so a numeric password is not turned into an integer.
 */
final readonly class ConfigurationFromEnvironment
{
    /** The value a committed settings.php holds where the environment has to provide it. */
    public const string PLACEHOLDER = 'REPLACED_BY_ENV';

    private const string PREFIX = 'TYPO3__';

    private const array STRINGS = [
        'SYS/encryptionKey',
        'SYS/trustedHostsPattern',
        'BE/installToolPassword',
        'DB/Connections/Default/host',
        'DB/Connections/Default/dbname',
        'DB/Connections/Default/user',
        'DB/Connections/Default/password',
        'MAIL/transport_smtp_server',
        'MAIL/transport_smtp_username',
        'MAIL/transport_smtp_password',
    ];

    /** A comma-separated value that becomes a list. */
    private const array LISTS = ['SYS/systemMaintainers'];

    /**
     * @param array<array-key, mixed> $configuration
     * @param array<string, string> $environment
     * @return array<array-key, mixed>
     */
    public function apply(array $configuration, array $environment): array
    {
        foreach ($environment as $name => $value) {
            $path = $this->path($name);
            if ($path === null) {
                continue;
            }

            $configuration = ArrayUtility::setValueByPath($configuration, $path, $this->cast($path, $value));
        }

        return $configuration;
    }

    /**
     * The environment variables a configuration still lacks: the ones for a path that is empty or still holds
     * the placeholder.
     *
     * @param array<array-key, mixed> $configuration
     * @param list<string> $paths
     * @return list<string>
     */
    public function missing(array $configuration, array $paths): array
    {
        $missing = [];
        foreach ($paths as $path) {
            // Only compared below
            // @mago-expect analysis:mixed-assignment
            $value = ArrayUtility::getValueByPath($configuration, $path);
            if (in_array($value, ['', null, self::PLACEHOLDER], strict: true)) {
                $missing[] = self::PREFIX . str_replace(search: '/', replace: '__', subject: $path);
            }
        }

        return $missing;
    }

    private function path(string $name): ?string
    {
        if (!str_starts_with($name, self::PREFIX)) {
            return null;
        }

        $path = str_replace(search: '__', replace: '/', subject: substr($name, strlen(self::PREFIX)));

        return in_array('', explode('/', $path), strict: true) ? null : $path;
    }

    /**
     * @return string|int|float|bool|list<string|int|float|bool|null>|null
     */
    private function cast(string $path, string $value): string|int|float|bool|array|null
    {
        if (in_array($path, self::LISTS, strict: true)) {
            return array_map(
                $this->scalar(...),
                array_values(array_filter(
                    array_map(trim(...), explode(',', $value)),
                    static fn(string $item): bool => $item !== '',
                )),
            );
        }

        return in_array($path, self::STRINGS, strict: true) ? $value : $this->scalar($value);
    }

    private function scalar(string $value): string|int|float|bool|null
    {
        return match (true) {
            preg_match('/^-?(0|[1-9]\d*)$/', $value) === 1 => (int)$value,
            preg_match('/^-?(0|[1-9]\d*)\.\d+$/', $value) === 1 => (float)$value,
            $value === 'true' => true,
            $value === 'false' => false,
            $value === 'null' => null,
            default => $value,
        };
    }
}
