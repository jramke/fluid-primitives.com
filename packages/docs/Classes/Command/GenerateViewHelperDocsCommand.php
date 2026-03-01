<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Command;

use ReflectionClass;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

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
        $sourceDir = $input->getArgument('source');
        $targetDir = $input->getArgument('target');

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $finder = new Finder();
        $finder->files()->in($sourceDir)->name('*ViewHelper.php');

        foreach ($finder as $file) {
            $className = $this->getClassNameFromFile($file->getRealPath());
            if (!$className) {
                continue;
            }

            $reflection = new ReflectionClass($className);

            // Skip abstract classes
            if ($reflection->isAbstract()) {
                continue;
            }

            $docComment = $reflection->getDocComment() ?: '';

            $shortName = $reflection->getShortName();
            $baseName = preg_replace('/ViewHelper$/', '', $shortName);
            $fileName = lcfirst($baseName) . '.md';
            $targetFile = $targetDir . '/' . $fileName;

            $arguments = $this->extractArguments($className);

            $markdown = $this->generateMarkdown($reflection, $docComment, $arguments);

            file_put_contents($targetFile, $markdown);
            $output->writeln("Generated: $targetFile");
        }

        return Command::SUCCESS;
    }

    private function getClassNameFromFile(string $filePath): ?string
    {
        $contents = file_get_contents($filePath);
        $contentsWithoutComments = preg_replace('#//.*|/\*[\s\S]*?\*/#', '', $contents);
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
     */
    private function extractArguments(string $className): array
    {
        $args = [];

        if (!is_subclass_of($className, AbstractViewHelper::class)) {
            return $args;
        }

        /** @var AbstractViewHelper $viewHelper */
        $viewHelper = new $className();
        $viewHelper->initializeArguments();

        $argumentDefinitions = $viewHelper->prepareArguments();

        foreach ($argumentDefinitions as $definition) {
            $defaultValue = $definition->getDefaultValue();

            // Format default value for display
            if ($defaultValue === null) {
                $defaultDisplay = 'null';
            } elseif (is_bool($defaultValue)) {
                $defaultDisplay = $defaultValue ? 'true' : 'false';
            } elseif (is_array($defaultValue)) {
                $defaultDisplay = empty($defaultValue) ? '[]' : json_encode($defaultValue);
            } elseif (is_string($defaultValue)) {
                $defaultDisplay = "'{$defaultValue}'";
            } else {
                $defaultDisplay = (string)$defaultValue;
            }

            $args[] = [
                'name' => $definition->getName(),
                'type' => $definition->getType(),
                'description' => $definition->getDescription(),
                'required' => $definition->isRequired(),
                'default' => $defaultDisplay,
            ];
        }

        return $args;
    }

    private function generateMarkdown(ReflectionClass $reflection, string $docComment, array $arguments): string
    {
        $shortName = $reflection->getShortName();
        $baseName = preg_replace('/ViewHelper$/', '', $shortName);
        $name = lcfirst($baseName);

        $content = $this->extractRawDocComment($docComment);

        $markdown = <<<MD
        <!-- This file is auto-generated by the docs:generate-viewhelper-docs command. Do not edit directly -->

        # ui:$name

        {% component: "ui:referenceButtons", arguments: { "name": "$shortName", "type": "viewhelper" } %}

        {$content}

        ## Arguments

        MD;

        if (empty($arguments)) {
            $markdown .= "\n_None_\n";
        } else {
            // Table header
            $markdown .= "\n| Name | Type | Description | Required | Default |\n";
            $markdown .= "|------|------|-------------|----------|--------|\n";

            foreach ($arguments as $arg) {
                // Escape pipe characters in description
                $description = str_replace('|', '\\|', $arg['description'] ?? '');
                $required = !empty($arg['required']) ? 'Yes' : 'No';
                $default = $arg['default'] ?? '';
                $type = str_replace('|', '\\|', $arg['type'] ?? '');
                $markdown .= "| `{$arg['name']}` | {$type} | {$description} | {$required} | {$default} |\n";
            }
        }

        return $markdown;
    }

    private function extractRawDocComment(string $docComment): string
    {
        // Remove /** at the start and */ at the end
        $docComment = preg_replace('#^/\*\*#', '', $docComment);
        $docComment = preg_replace('#\*/$#', '', $docComment);

        $lines = explode("\n", $docComment);
        $cleanLines = [];

        foreach ($lines as $line) {
            // Remove leading * and one optional space after it
            $line = preg_replace('/^\s*\*\s?/', '', $line);
            $cleanLines[] = $line;
        }

        // Preserve all original line breaks exactly
        return implode("\n", $cleanLines);
    }
}
