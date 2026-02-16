# AGENTS.md - Fluid Primitives

This document provides guidance for AI coding agents working in this repository.

## Project Overview

Fluid Primitives is a headless component library for TYPO3 Fluid templating. It provides UI primitives (Accordion, Dialog, Tabs, Select, etc.) that are server-rendered with PHP/Fluid and hydrated client-side with TypeScript using Zag.js state machines.

**Key technologies:**

- PHP 8.2+ (TYPO3 extension, ViewHelpers, Contexts)
- TypeScript (client-side hydration using Zag.js)
- TYPO3 Fluid (HTML-like templating language)
- Tailwind CSS v4 (documentation site styling)

## Project Structure

```
packages/
├── fluid-primitives/          # Main library (NPM + Composer package)
│   ├── Classes/               # PHP: ViewHelpers, Contexts, Services
│   ├── Resources/Private/
│   │   ├── Client/src/lib/    # Core TS: Component, Machine, etc.
│   │   └── Primitives/        # Component implementations
│   │       └── [Name]/        # e.g., Accordion/, Dialog/
│   │           ├── *.ts       # Component class
│   │           ├── *.entry.ts # Auto-mount entry
│   │           └── *.html     # Fluid templates
│   └── Resources/Public/      # Built JS output
└── docs/                      # Documentation website (TYPO3 sitepackage)
```

## Build/Development Commands

```bash
# Local development (requires DDEV)
ddev start
ddev composer install
ddev snapshot restore --latest
ddev npm install
ddev npm run dev              # Runs both primitives:dev and docs:dev

# Individual commands
npm run primitives:build      # Build the fluid-primitives package
npm run primitives:dev        # Watch mode for primitives
npm run docs:build            # Build documentation site
npm run docs:dev              # Dev server for docs (port 5173)

# Code quality
npm run format                # Format all files with Prettier
npm run format:check          # Check formatting without writing
npm run types                 # TypeScript type checking (tsc --noEmit)
```

## Testing

**No test framework is currently configured.** There are no test files or test commands.

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

Run `npm run format` before committing changes.

### EditorConfig

- **Unix line endings (LF)** for all files
- **Tabs** for: JS, TS, TSX, JSX, CSS, SCSS (size 4)
- **Spaces** for: PHP, HTML, YAML, JSON, Markdown (size 4, YAML size 2)

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

(() => {
    mount('accordion', ({ props }) => {
        const accordion = new Accordion(props);
        accordion.init();
        return accordion;
    });
})();
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
