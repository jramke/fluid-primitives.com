<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Command;

use FluidPrimitives\Docs\Utility\DocsUtility;
use Jramke\FluidPrimitives\Utility\Typed;
use ReflectionClass;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\ArgumentDefinition;

#[AsCommand(name: 'docs:generate-viewhelper-docs', description: 'Generate documentation for Fluid ViewHelpers')]
class GenerateViewHelperDocsCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setDescription('Generate Fluid ViewHelper API documentation in Markdown')
            ->addArgument(
                'source',
                InputArgument::OPTIONAL,
                'Source folder containing ViewHelper classes',
                'packages/fluid-primitives/Classes/ViewHelpers',
            )
            ->addArgument(
                'target',
                InputArgument::OPTIONAL,
                'Target folder to write the markdown files into',
                'packages/docs/Resources/Private/Content/docs/viewhelpers',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sourceDir = Typed::string($input->getArgument('source'));
        $targetDir = Typed::string($input->getArgument('target'));

        if (!is_dir($targetDir)) {
            mkdir($targetDir, permissions: 0o777, recursive: true);
        }

        $finder = new Finder();
        $finder->files()->in($sourceDir)->name('*ViewHelper.php');

        foreach ($finder as $file) {
            $realPath = $file->getRealPath();
            if ($realPath === false) {
                continue;
            }

            $className = $this->getClassNameFromFile($realPath);
            if ($className === null || !class_exists($className)) {
                continue;
            }

            $reflection = new ReflectionClass($className);

            // Skip abstract classes
            if ($reflection->isAbstract()) {
                continue;
            }

            $docComment = $reflection->getDocComment() ?: '';

            $shortName = $reflection->getShortName();
            $baseName = preg_replace('/ViewHelper$/', replacement: '', subject: $shortName);
            $fileName = lcfirst((string)$baseName) . '.md';
            $targetFile = $targetDir . '/' . $fileName;

            $arguments = $this->extractArguments($className);

            $markdown = $this->generateMarkdown($reflection, $docComment, $arguments);

            file_put_contents($targetFile, $markdown);
            $output->writeln("Generated: {$targetFile}");
        }

        return Command::SUCCESS;
    }

    private function getClassNameFromFile(string $filePath): ?string
    {
        $contents = file_get_contents($filePath) ?: '';
        $contentsWithoutComments =
            preg_replace('#//.*|/\*[\s\S]*?\*/#', replacement: '', subject: $contents) ?? $contents;

        $ns = [];
        $cls = [];
        if (
            preg_match('/namespace\s+([^;]+);/', $contentsWithoutComments, $ns) &&
            preg_match('/class\s+([^\s]+)/', $contentsWithoutComments, $cls)
        ) {
            return $ns[1] . '\\' . $cls[1];
        }
        return null;
    }

    /**
     * Extract arguments by instantiating the ViewHelper and reading its argument definitions.
     * This is the same approach used by the official TYPO3 Fluid documentation generator.
     *
     * Resolved via `GeneralUtility::makeInstance()` rather than a plain `new` - some ViewHelpers
     * (e.g. `FileUploadDeleteCheckboxViewHelper`) declare constructor-injected dependencies (like
     * `HashService`), which only `makeInstance()` can supply via the DI container; a bare `new`
     * fails with a missing-argument error for any of those.
     *
     * @return list<array{name: string, type: string, description: string, required: bool, default: string}>
     */
    private function extractArguments(string $className): array
    {
        $args = [];

        if (!is_subclass_of($className, AbstractViewHelper::class)) {
            return $args;
        }

        $viewHelper = GeneralUtility::makeInstance($className);
        $viewHelper->initializeArguments();

        /** @var ArgumentDefinition[] $argumentDefinitions */
        $argumentDefinitions = $viewHelper->prepareArguments();

        foreach ($argumentDefinitions as $definition) {
            $args[] = [
                'name' => $definition->getName(),
                'type' => DocsUtility::displayType($definition->getType()),
                'description' => $definition->getDescription(),
                'required' => $definition->isRequired(),
                'default' => DocsUtility::displayValue($definition->getDefaultValue()),
            ];
        }

        return $args;
    }

    /**
     * @param list<array{name: string, type: string, description: string, required: bool, default: string}> $arguments
     */
    private function generateMarkdown(ReflectionClass $reflection, string $docComment, array $arguments): string
    {
        $shortName = $reflection->getShortName();
        $baseName = preg_replace('/ViewHelper$/', replacement: '', subject: $shortName);
        $name = lcfirst((string)$baseName);

        $content = $this->extractRawDocComment($docComment);

        $markdown = <<<MD
        <!-- This file is auto-generated by the docs:generate-viewhelper-docs command. Do not edit directly -->

        # ui:{$name}

        {% component: "ui:referenceButtons", arguments: { "name": "{$shortName}", "type": "viewhelper" } %}

        {$content}

        ## Arguments

        MD;

        if ($arguments === []) {
            $markdown .= "\n_None_\n";
            return $markdown;
        }

        // TODO: align with structure from component api table
        // Table header
        $markdown .= "\n| Name | Type | Description | Required | Default |\n";
        $markdown .= "|------|------|-------------|----------|--------|\n";

        foreach ($arguments as $arg) {
            // Escape pipe characters in description
            $description = htmlspecialchars(str_replace('|', replace: '\\|', subject: $arg['description']));
            $required = $arg['required'] ? 'Yes' : 'No';
            $default = $arg['default'];
            $type = str_replace('|', replace: '\\|', subject: $arg['type']);
            $markdown .= "| `{$arg['name']}` | {$type} | {$description} | {$required} | {$default} |\n";
        }

        return $markdown;
    }

    private function extractRawDocComment(string $docComment): string
    {
        // Remove /** at the start and */ at the end
        $docComment = preg_replace('#^/\*\*#', replacement: '', subject: $docComment);
        $docComment = preg_replace('#\*/$#', replacement: '', subject: (string)$docComment);

        $lines = explode("\n", (string)$docComment);
        $cleanLines = [];

        foreach ($lines as $line) {
            // Remove leading * and one optional space after it
            $line = preg_replace('/^\s*\*\s?/', replacement: '', subject: $line) ?? $line;
            $cleanLines[] = $line;
        }

        // Preserve all original line breaks exactly
        return implode("\n", $cleanLines);
    }
}
