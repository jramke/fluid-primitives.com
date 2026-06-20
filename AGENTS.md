# AGENTS.md - Fluid Primitives

This document provides guidance for AI coding agents working in this repository.

## Project Overview

Fluid Primitives is a headless component library for TYPO3 Fluid templating. It provides UI primitives (Accordion, Dialog, Tabs, Select, etc.) that are server-rendered with PHP/Fluid and hydrated client-side with TypeScript using Zag.js state machines.

You can check out the docs `.md` files under `packages/docs/Resources/Private/Content/docs` or the index/nav under `packages/docs/Resources/Private/Content/nav.yaml` for more informations about core concepts of the library or some components.

**Key technologies:**

- PHP 8.3 (TYPO3 extension, ViewHelpers, Contexts)
- TypeScript (client-side hydration using Zag.js)
- TYPO3 Fluid (HTML-like templating language)
- Tailwind CSS v4 (documentation site and registry styling)

## Project Structure

```txt
packages/
├── fluid-primitives/          # Main library (NPM + Composer package)
│   ├── Classes/               # PHP: ViewHelpers, Contexts, Services
│   ├── Resources/Private/
│   │   ├── Client/src/lib/    # Core TS: Component, Machine, etc.
│   │   └── Primitives/        # Component implementations
│   │       └── [Name]/        # e.g., Accordion/, Dialog/
│   │           ├── *.ts       # Component class
│   │           ├── *.entry.ts # Client-Side component entry files
│   │           └── *.html     # Fluid templates
└── docs/                      # Documentation website (TYPO3 sitepackage)
```

## Build/Development Commands

**Important:** Always use `ddev` prefix for `composer` and `npm` commands in this project.

```bash
# Local development (requires DDEV)
ddev start
ddev composer install
ddev snapshot restore --latest
ddev npm install
ddev npm run dev              # Runs both primitives:dev and docs:dev

# Individual commands
ddev npm run primitives:build # Build the fluid-primitives package
ddev npm run primitives:dev   # Watch mode for primitives
ddev npm run docs:build       # Build documentation site
ddev npm run docs:dev         # Dev server for docs (port 5173)

# Code quality
ddev npm run format           # Format all files with Prettier
ddev npm run format:check     # Check formatting without writing
ddev npm run types            # TypeScript type checking (tsc --noEmit)
ddev composer run format      # Format PHP files with Mago, always run after changes are done
ddev composer run lint        # Static analysis for PHP with Mago and Rector
```

## Testing

Tests are located in `packages/fluid-primitives/tests/` and use PHPUnit with the TYPO3 testing framework.

### Test Structure

```txt
packages/fluid-primitives/tests/
├── Unit/                      # Core infrastructure tests (utilities, registry, base classes)
├── Functional/                # Full TYPO3 tests with database (SQLite)
│   ├── ViewHelpers/           # ViewHelper rendering tests
│   └── Components/            # Component rendering tests (all primitives go here)
├── Bootstrap.php              # Test bootstrap (autoloader + TYPO3 testing framework)

├── TestCase.php               # Base class for unit tests
└── ViewHelperTestCase.php     # Base class for ViewHelper tests
```

**Where to put tests:**

- **Functional/Components/** - All component tests (Accordion, Checkbox, Dialog, etc.). These test the full integration: context logic → Fluid template → HTML output. This ensures the context is correct AND the template uses it correctly.
- **Unit/** - Core infrastructure only (ComponentUtility, HydrationRegistry, TagAttributes, AbstractComponentContext). These are utilities/base classes not tied to specific components.

### Running Tests

```bash
# From monorepo root (recommended for development)
ddev composer test              # Run all tests
ddev composer test:unit         # Run unit tests
ddev composer test:functional   # Run functional tests

# From package directory (used by GitHub Actions)
cd packages/fluid-primitives
ddev composer test              # Run all tests
```

### Test Quality Guidelines

Write meaningful tests that justify their existence. Avoid trivial tests.

**DO NOT write tests for:**

- Empty/null inputs returning empty/null outputs (e.g., `expect(new Foo())->toBeEmpty()`)
- Simple getter/setter behavior
- Single-line wrapper functions around standard library calls
- Obvious type coercions (e.g., `'Accordion'` → `'accordion'`)
- Edge cases that test the same code path as existing tests

**DO write tests for:**

- Complex business logic with multiple code paths
- Integration points (e.g., HydrationRegistry + AssetCollector)
- Error conditions and exception handling
- State management logic (e.g., expanded/disabled states)
- Security-relevant behavior (e.g., HTML escaping)

**Consolidate related tests** into single test cases when they verify the same behavior:

```php
// BAD: Multiple trivial tests
#[Test]
public function returnsFalseWhenValueNotInArray(): void
#[Test]
public function returnsTrueWhenValueInArray(): void
#[Test]
public function handlesMultipleValues(): void

// GOOD: Single consolidated test
#[Test]
public function returnsExpandedBasedOnDefaultValueArrayMembership(): void
it('returns expanded based on defaultValue array membership', function () {
    $this->assertTrue($context->getItemState(['value' => 'item-1'])->expanded);
    $this->assertFalse($context->getItemState(['value' => 'item-3'])->expanded);
});
```

**Test names should describe behavior, not implementation:**

```php
// BAD
#[Test]
public function itCallsStr_containsWithCorrectArguments(): void

// GOOD
#[Test]
public function skipsPrimitivesNamespacesWhenExtractingBaseName(): void
```

## Code Formatting

### Prettier Configuration

Uses Prettier with `prettier-plugin-organize-imports` for automatic import sorting.

```json
{
    "printWidth": 100,
    "singleQuote": true,
    "trailingComma": "es5",
    "bracketSpacing": true,
    "arrowParens": "avoid",
    "semi": true
}
```

We also use Mago for formatting PHP files.

Run `ddev npm run format` and `ddev composer run format` before committing changes.

### EditorConfig

- **Unix line endings (LF)** for all files
- **Spaces** for everything (size 4, YAML size 2)

## TypeScript Guidelines

### Compiler Settings

- Strict mode enabled (`strict: true`)
- No unused locals or parameters (`noUnusedLocals`, `noUnusedParameters`)
- ES Modules (`"type": "module"`)
- Target: ESNext with DOM libs

### Import Style

```typescript
// Namespace imports for Zag.js packages
import * as accordion from '@zag-js/accordion';

// Named imports for internal modules
import { Component, Machine, normalizeProps } from '../../Client';

// Type-only imports when importing only types
import type { FieldMachine } from '../Field/src/field.registry';
```

### Component Class Pattern

Each primitive extends `Component<Props, Api>`:

```typescript
export class Accordion extends Component<accordion.Props, accordion.Api> {
    static name = 'accordion'; // Required: lowercase component name

    initMachine(props: accordion.Props): Machine<any> {
        return new Machine(accordion.machine, { ...defaultProps, ...props });
    }

    initApi() {
        return accordion.connect(this.machine.service, normalizeProps);
    }

    render() {
        // Hydrate DOM elements with state machine props
        const rootEl = this.getElement('root');
        if (rootEl) this.spreadProps(rootEl, this.api.getRootProps());
        // ... hydrate other elements
    }
}
```

### Entry File Pattern

Each component has an auto-mount entry file (`*.entry.ts`):

```typescript
import { mount } from '../../Client';
import { Accordion } from './Accordion';

mount('accordion', ({ props }) => {
    const accordion = new Accordion(props);
    accordion.init();
    return accordion;
});
```

### Naming Conventions (TypeScript)

- **PascalCase**: Classes (`Accordion`, `Component`, `Machine`)
- **camelCase**: Methods, variables, functions (`initMachine`, `spreadProps`)
- **Static `name`**: Required on component classes (lowercase)

## PHP Guidelines

### Namespace Structure

```php
namespace Jramke\FluidPrimitives\Contexts;
namespace Jramke\FluidPrimitives\ViewHelpers;
namespace Jramke\FluidPrimitives\Service;
```

### Strict Types

Always use strict types declaration:

```php
<?php

declare(strict_types=1);
```

### ViewHelper Pattern

```php
class ExampleViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('name', 'string', 'Description', true);
        $this->registerArgument('optional', 'boolean', 'Description', false, false);
    }

    public function render(): mixed
    {
        // Implementation
    }
}
```

### Context Class Pattern

```php
class AccordionContext extends AbstractComponentContext
{
    public function getItemState(array $item): object
    {
        // Return state object for template consumption
    }
}
```

## Fluid Template Guidelines

### Props Definition

```html
<ui:prop name="variant" type="string" optional="{true}" default="primary" /> <ui:prop name="disabled" type="boolean" optional="{true}" client="{true}" />
```

### Ref Pattern (for hydration)

```html
<div
    {f:if(condition: class, then: 'class="{class}"')}
    {ui:ref(name: 'root', data: refData)}
    {ui:attributes()}>
    <f:slot />
</div>
```

## Error Handling

### TypeScript

- Throw descriptive errors with context
- Use null checks with optional chaining (`?.`) and nullish coalescing (`??`)
- Check for required props in constructors

### PHP

- Throw `\RuntimeException` with error codes for ViewHelper validation
- Include descriptive error messages with context
