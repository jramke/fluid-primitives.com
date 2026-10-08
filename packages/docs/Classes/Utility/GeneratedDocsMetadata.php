<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Utility;

use RuntimeException;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Reads the generated docs data under `Content/generated/`: the Zag.js metadata (`docs:generate-zag-docs`),
 * and the data derived from the sources by `npm run docs:generate` - the self-made machines and the client
 * utilities. A machine in `machines/` wins over `zag-docs/`, so the directory a primitive was found in is
 * its provenance.
 */
final class GeneratedDocsMetadata
{
    public const string GENERATED_DIRECTORY = 'EXT:docs/Resources/Private/Content/generated';
    public const string ZAG_DOCS_DIRECTORY = self::GENERATED_DIRECTORY . '/zag-docs';
    public const string MACHINES_DIRECTORY = self::GENERATED_DIRECTORY . '/machines';
    public const string CLIENT_API_FILE = self::GENERATED_DIRECTORY . '/client-api.json';
    public const string SOURCE_DIRECTORY = 'node_modules/@zag-js/docs/data';

    public const string SOURCE_MACHINE = 'machine';
    public const string SOURCE_ZAG = 'zag';

    /**
     * The generated data of the primitive (`accessibility`, `api`, `dataAttributes`) plus its `source`.
     *
     * @return array<array-key, mixed>
     */
    public static function forMachine(string $primitive): array
    {
        $normalizedPrimitive = self::normalizePrimitiveName($primitive);
        $source = self::sourceFor($normalizedPrimitive);

        if ($source === null) {
            throw new RuntimeException(
                sprintf(
                    'Docs metadata for "%s" is missing. Run `npm run docs:generate` (self-made machine) or the %s command (Zag.js machine) first.',
                    $normalizedPrimitive,
                    'docs:generate-zag-docs',
                ),
                2329929121,
            );
        }

        $data = self::readJsonFile(self::fileForMachine($normalizedPrimitive, $source));
        if ($data === []) {
            throw new RuntimeException(
                sprintf('Generated docs metadata for "%s" is empty. Regenerate it first.', $normalizedPrimitive),
                1057914033,
            );
        }

        return ['source' => $source, ...$data];
    }

    /**
     * Where the metadata of a primitive comes from, `null` when there is none.
     */
    public static function sourceFor(string $primitive): ?string
    {
        $normalizedPrimitive = self::normalizePrimitiveName($primitive);

        foreach ([self::SOURCE_MACHINE, self::SOURCE_ZAG] as $source) {
            if (is_file(self::fileForMachine($normalizedPrimitive, $source))) {
                return $source;
            }
        }

        return null;
    }

    /**
     * @return array<array-key, mixed>
     */
    public static function forSymbol(string $symbol): array
    {
        $clientApi = self::readJsonFile(self::absolutePath(self::CLIENT_API_FILE));
        // Narrowed right below via is_array() - JSON has no static shape here.
        // @mago-expect analysis:mixed-assignment
        $entry = $clientApi[$symbol] ?? null;

        if (!is_array($entry)) {
            throw new RuntimeException(
                sprintf(
                    'No client API docs for "%s". Check it is exported from the client index, then run `npm run docs:generate`.',
                    $symbol,
                ),
                1782048611,
            );
        }

        return $entry;
    }

    public static function generatedDirectory(): string
    {
        return self::absolutePath(self::ZAG_DOCS_DIRECTORY);
    }

    public static function sourceDirectory(): string
    {
        return Environment::getProjectPath() . '/' . self::SOURCE_DIRECTORY;
    }

    private static function fileForMachine(string $primitive, string $source): string
    {
        $directory = $source === self::SOURCE_MACHINE ? self::MACHINES_DIRECTORY : self::ZAG_DOCS_DIRECTORY;

        return self::absolutePath($directory) . '/' . $primitive . '.json';
    }

    private static function absolutePath(string $path): string
    {
        return GeneralUtility::getFileAbsFileName($path);
    }

    /**
     * @return array<array-key, mixed>
     */
    private static function readJsonFile(string $file): array
    {
        if (!is_file($file)) {
            throw new RuntimeException(sprintf('Generated docs file not found: %s', $file), 9102163250);
        }

        $contents = file_get_contents($file);
        if ($contents === false) {
            throw new RuntimeException(sprintf('Unable to read generated docs file: %s', $file), 1929703906);
        }

        // Narrowed immediately below via is_array() - JSON has no static shape here.
        // @mago-expect analysis:mixed-assignment
        $decoded = json_decode($contents, associative: true, depth: 512, flags: JSON_THROW_ON_ERROR);

        return is_array($decoded) ? $decoded : [];
    }

    private static function normalizePrimitiveName(string $primitive): string
    {
        $normalized = trim($primitive, characters: " \n\r\t/\0\x0B");
        $normalized = preg_replace('/(?<!^)([A-Z])/', replacement: '-$1', subject: $normalized) ?? $normalized;
        $normalized = str_replace(['_', ' '], replace: '-', subject: $normalized);

        return strtolower($normalized);
    }
}
