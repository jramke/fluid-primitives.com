<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Command;

use FluidPrimitives\Docs\Utility\ZagDocsMetadata;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[AsCommand(name: 'docs:generate-zag-docs', description: 'Generate static Zag.js metadata for docs tables')]
class GenerateZagDocsCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setDescription('Generate static metadata from @zag-js/docs for primitive API and accessibility tables')
            ->addArgument(
                'source',
                InputArgument::OPTIONAL,
                'Source folder containing @zag-js/docs data files',
                Environment::getProjectPath() . '/' . ZagDocsMetadata::SOURCE_DIRECTORY,
            )
            ->addArgument(
                'target',
                InputArgument::OPTIONAL,
                'Directory for the generated metadata JSON files',
                ZagDocsMetadata::GENERATED_DIRECTORY,
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sourceDir = $input->getArgument('source');
        $targetDir = GeneralUtility::getFileAbsFileName((string)$input->getArgument('target'));

        if (!is_dir($sourceDir)) {
            $output->writeln(sprintf('<error>Source folder not found: %s</error>', $sourceDir));
            $output->writeln(sprintf('<error>Run %s to generate the files first.</error>', 'docs:generate-zag-docs'));
            return Command::FAILURE;
        }

        if (!is_dir($targetDir) && !mkdir($targetDir, 0o777, true) && !is_dir($targetDir)) {
            $output->writeln(sprintf('<error>Unable to create target folder: %s</error>', $targetDir));
            return Command::FAILURE;
        }

        $files = [
            'accessibility.json' => 'accessibility',
            'api.json' => 'api',
            'data-attr.json' => 'dataAttributes',
        ];

        $primitiveNames = [];
        foreach ($files as $fileName => $key) {
            $json = $this->readJsonFile($sourceDir . '/' . $fileName);
            foreach (array_keys($json) as $primitiveName) {
                $primitiveNames[$primitiveName] = true;
            }
        }

        foreach (array_keys($primitiveNames) as $primitiveName) {
            $metadata = [
                'accessibility' => $this->readPrimitiveEntry($sourceDir . '/accessibility.json', $primitiveName),
                'api' => $this->readPrimitiveEntry($sourceDir . '/api.json', $primitiveName),
                'dataAttributes' => $this->readPrimitiveEntry($sourceDir . '/data-attr.json', $primitiveName),
            ];

            $targetFile = rtrim($targetDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $primitiveName . '.json';
            file_put_contents(
                $targetFile,
                json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL,
            );
            $output->writeln(sprintf('Generated: %s', $targetFile));
        }

        return Command::SUCCESS;
    }

    private function readPrimitiveEntry(string $jsonFile, string $primitiveName): array
    {
        $data = $this->readJsonFile($jsonFile);
        return is_array($data[$primitiveName] ?? null) ? $data[$primitiveName] : [];
    }

    private function readJsonFile(string $file): array
    {
        if (!is_file($file)) {
            throw new RuntimeException(sprintf('Missing Zag docs file: %s', $file));
        }

        $contents = file_get_contents($file);
        if ($contents === false) {
            throw new RuntimeException(sprintf('Unable to read Zag docs file: %s', $file));
        }

        $decoded = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

        return is_array($decoded) ? $decoded : [];
    }
}
