<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Utility;

use RuntimeException;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class ZagDocsMetadata
{
    public const string GENERATED_DIRECTORY = 'EXT:docs/Resources/Private/Content/generated/zag-docs';
    public const string SOURCE_DIRECTORY = 'node_modules/@zag-js/docs/data';

    public static function forPrimitive(string $primitive): array
    {
        $normalizedPrimitive = self::normalizePrimitiveName($primitive);
        $generatedFile = self::generatedFileForPrimitive($normalizedPrimitive);

        if (!is_file($generatedFile)) {
            throw new RuntimeException(
                sprintf(
                    'Zag docs metadata for "%s" is missing. Run the %s command first to generate it in %s.',
                    $normalizedPrimitive,
                    'docs:generate-zag-docs',
                    self::generatedDirectory(),
                ),
                2329929121,
            );
        }

        $data = self::readJsonFile($generatedFile);
        if ($data === []) {
            throw new RuntimeException(
                sprintf(
                    'Generated Zag docs metadata for "%s" is empty. Run the %s command first.',
                    $normalizedPrimitive,
                    'docs:generate-zag-docs',
                ),
                1057914033,
            );
        }

        return $data;
    }

    private static function readJsonFile(string $file): array
    {
        if (!is_file($file)) {
            throw new RuntimeException(sprintf('Zag docs file not found: %s', $file), 9102163250);
        }

        $contents = file_get_contents($file);
        if ($contents === false) {
            throw new RuntimeException(sprintf('Unable to read Zag docs file: %s', $file), 1929703906);
        }

        // Narrowed immediately below via is_array() - JSON has no static shape here.
        // @mago-expect analysis:mixed-assignment
        $decoded = json_decode($contents, associative: true, depth: 512, flags: JSON_THROW_ON_ERROR);

        return is_array($decoded) ? $decoded : [];
    }

    public static function generatedDirectory(): string
    {
        return GeneralUtility::getFileAbsFileName(self::GENERATED_DIRECTORY);
    }

    public static function sourceDirectory(): string
    {
        return Environment::getProjectPath() . '/' . self::SOURCE_DIRECTORY;
    }

    private static function generatedFileForPrimitive(string $primitive): string
    {
        return self::generatedDirectory() . '/' . $primitive . '.json';
    }

    private static function normalizePrimitiveName(string $primitive): string
    {
        $normalized = trim($primitive, characters: " \n\r\t/\0\x0B");
        $normalized = preg_replace('/(?<!^)([A-Z])/', replacement: '-$1', subject: $normalized) ?? $normalized;
        $normalized = str_replace(['_', ' '], replace: '-', subject: $normalized);

        return strtolower($normalized);
    }
}
