# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Fluid Primitives is a headless component library for TYPO3 Fluid templating. It provides UI primitives (Accordion, Dialog, Tabs, Select, etc.) that are server-rendered with PHP/Fluid and hydrated client-side with TypeScript using Zag.js state machines.

You can check out the docs `.md` files under `packages/docs/Resources/Private/Content/docs` or the index/nav under `packages/docs/Resources/Private/Content/nav.yaml` for more informations about core concepts of the library or some components.

**Key technologies:**

- PHP 8.3 (TYPO3 extension, ViewHelpers, Contexts)
- TypeScript (client-side hydration using Zag.js)
- TYPO3 Fluid (HTML-like templating language)
- Tailwind CSS v4 (documentation site and registry styling)

## Design Principles

**Prefer a structural/naming fix over an internal flag.** Before adding another parameter, flag,
or special-case branch to make an edge case work, ask whether a small change to naming, folder
structure, or public API shape removes the need for it entirely - even at a small DX cost - as
long as the new shape keeps a similar level of DX while meaningfully simplifying long-term
maintenance. Default to the structural fix when it isn't clearly worse; if genuinely undecidable,
ask rather than defaulting to the flag. This applies now, pre-v1 (breaking changes are cheap), and
still applies post-v1 - a breaking structural fix can still be the right call later if it holds DX
steady while cutting maintenance cost; being past v1 doesn't by itself make accumulating internal
flags the correct default.

Example: a folder-shape-detected independent root example (`CheckboxGroup/Examples/SelectAll.fluid.html`)
shared a hydration/context bucket with the real `CheckboxGroup` component, because base-name
derivation only looks at the first dotted segment. The fix wasn't a new parameter on
`getComponentBaseNameFromViewHelperName()` to special-case it - it was not letting a component's
own folder also be the parent of an unrelated nested example in the first place (move examples out
of the component's own folder, e.g. `ui:checkboxGroupExamples.selectAll` or
`ui:examples.checkboxGroup.selectAll`, not `ui:checkboxGroup.examples.selectAll`).

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
# Individual commands
ddev npm run primitives:build # Build the fluid-primitives package
ddev npm run primitives:dev   # Watch mode for primitives
ddev npm run primitives:test  # Client unit tests (vitest + jsdom)
ddev npm run docs:build       # Build documentation site
ddev npm run docs:dev         # Dev server for docs (port 5173)
ddev npm run docs:generate    # Regenerate the API docs data (client utilities, self-made machines)
ddev npm run docs:generate:check  # Fail if the generated API docs data is outdated or a JSDoc is missing

# Code quality
ddev npm run format           # Format all files with Prettier
ddev npm run format:check     # Check formatting without writing
ddev npm run types            # TypeScript type checking (tsc --noEmit)
ddev composer run format      # Format PHP files with Mago, always run after changes are done
ddev composer run lint        # Static analysis for PHP with Mago and Rector
ddev composer mago:analyze    # Analyze PHP code quality with Mago
```

## Testing

Tests are located in `packages/fluid-primitives/tests/`. PHP code uses PHPUnit with the TYPO3 testing framework, client TypeScript uses vitest with jsdom (`tests/Client/`, run with `ddev npm run primitives:test`).

### Test Structure

```txt
packages/fluid-primitives/tests/
├── Unit/                      # Core infrastructure tests (utilities, registry, base classes)
├── Functional/                # Full TYPO3 tests with database (SQLite)
│   ├── ViewHelpers/           # ViewHelper rendering tests
│   └── Components/            # Component rendering tests (all primitives go here)
├── Client/                    # vitest + jsdom tests for pure client logic (machine utilities, Form helpers)
├── Bootstrap.php              # Test bootstrap (autoloader + TYPO3 testing framework)

├── TestCase.php               # Base class for unit tests
└── ViewHelperTestCase.php     # Base class for ViewHelper tests
```

**Where to put tests:**

- **Functional/Components/** - All component tests (Accordion, Checkbox, Dialog, etc.). These test the full integration: context logic → Fluid template → HTML output. This ensures the context is correct AND the template uses it correctly.
- **Unit/** - Core infrastructure only (ComponentUtility, HydrationRegistry, TagAttributes, AbstractComponentContext). These are utilities/base classes not tied to specific components.
- **Client/** - TypeScript logic that can run without a browser: pure helpers, machine guards/computed values, DOM helpers that jsdom can model. Mirror the source path (`Client/Form/form.path.test.ts` for `Primitives/Form/src/form.path.ts`). Behaviour that needs real focus, layout or hydration order is verified in the docs site instead.

### Running Tests

```bash
# From monorepo root (recommended for development)
ddev composer test              # Run all tests
ddev composer test:unit         # Run unit tests
ddev composer test:functional   # Run functional tests

# From package directory (used by GitHub Actions)
cd packages/fluid-primitives
ddev composer test              # Run all tests

# Client TypeScript tests
ddev npm run primitives:test    # Run vitest once
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

## Code style

- PHP: [mago](https://mago.carthage.software) (`mago.toml`) + [rector](https://github.com/rectorphp/rector)
  (`rector.php`) for TYPO3-version portability.
- TS/JS: Prettier (`.prettierrc`).
- Indentation: per `.editorconfig` (4 spaces, 2 for YAML).
- Comments: bare minimum — only for a non-obvious constraint or mechanism, never restating what the code does.

## JSDoc for the public surface

The API docs of the client utilities (`Client/src/lib`) and of the self-made machines
(`Primitives/<Name>/src/*.machine.ts`) are generated from the TypeScript sources by
`packages/docs/scripts/generate-api-docs.mjs` into `Content/generated/` (committed). Rerun
`docs:generate` after touching a JSDoc or a public signature - `docs:generate:check` fails otherwise.

- **Everything in a JSDoc block lands in the docs.** Maintainer-only notes belong in `//` comments.
- Every export, public member, prop and API member needs a summary. Mark what is not public with `@internal`.
- Tags: `@param name - text`, `@returns`, `@default`, `@example`, `@deprecated`, `{@link X}` (rendered as inline
  code). Any other tag is an error, so put a `@scope/package` name in backticks.
- A `data-*` attribute rendered by a machine needs a JSDoc on its key in the `connect.ts`, unless it is one of the
  standard state attributes (`data-disabled`, `-invalid`, `-valid`, `-required`, `-readonly`, `-touched`, `-dirty`,
  `-filled`, `-focus`).
- Show it on a page with `{% component: "ui:apiReference", arguments: { "symbol": "ExportName" } %}`. The page author
  writes the heading. A self-made machine needs nothing on its page, `ui:componentPropsTable` picks up
  `generated/machines/<name>.json` before `generated/zag-docs/<name>.json`.
- A heading in a component template that should appear in the table of contents is written `<h3 data-toc>` (or `h2`),
  `ComponentHeadingsExtension` turns it into a real heading with an id and a permalink.

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
    static componentName = 'accordion'; // Required: lowercase component name. Not `name` - every
    // class (even a subclass that redeclares nothing) gets its own auto-assigned
    // Function.prototype.name, which would silently shadow an inherited `static name`.

    initMachine(props: accordion.Props): Machine<any> {
        return new Machine(accordion.machine, { ...defaultProps, ...props });
    }

    initApi() {
        return accordion.connect(this.machine.service, normalizeProps);
    }

    render() {
        // Hydrate DOM elements with state machine props
        const rootEl = this.query('root');
        if (rootEl) this.spreadProps(rootEl, this.api.getRootProps());
        // ... hydrate other elements
    }
}
```

`new Accordion(props)` only stores the props. `init()` runs `transformProps()` once - its result is
`userProps` - then builds the hydrator, machine and api from it (`initHydrator()`, `initMachine()`,
`initApi()`), renders and starts the machine. So `machine`/`api` don't exist before `init()`, and a
field assigned inside `initMachine()` is safe: no overridable hook runs from the base constructor,
before the subclass' field initializers.

### Entry File Pattern

Each component has an auto-mount entry file (`*.entry.ts`):

```typescript
import { mountAll } from '../../Client';
import { Accordion } from './Accordion';

mountAll('accordion', ({ props }) => {
    const accordion = new Accordion(props);
    accordion.init();
    return accordion;
});
```

### Naming Conventions (TypeScript)

- **PascalCase**: Classes (`Accordion`, `Component`, `Machine`)
- **camelCase**: Methods, variables, functions (`initMachine`, `spreadProps`)
- **Static `componentName`**: Required on component classes (lowercase) - deliberately not `name`, see the Component Class Pattern above

## PHP Guidelines

### Class Organization

Namespaces mirror `Classes/` subfolders 1:1 (PSR-4):

```php
namespace Jramke\FluidPrimitives\Contexts;
namespace Jramke\FluidPrimitives\ViewHelpers;
namespace Jramke\FluidPrimitives\Service;
```

`Classes/` is organized by what each class _is_, not by which class it was originally split out of - when extracting a collaborator, place it in the folder matching its role, not next to the class it came from.

- **`Contexts/`** - per-component state exposed to templates as `context.*` (see Context Class Pattern below).
- **`ViewHelpers/`** - Fluid ViewHelpers, the `ui:` namespace.
- **`Service/`** - stateless or DI'd collaborators with real behavior. `Service/Component/` holds `ComponentRenderer`'s own rendering-pipeline collaborators specifically (argument/identity resolution, context merging, hydration collection).
- **`Factory/`** - classes with a `create()` method that build and wire up another object, typically because that object needs a runtime value (a dynamic class name, the caller's own `$this`) that can't be autowired - see Dependency Injection below.
- **`Domain/Dto/`** - plain data carriers with no framework dependencies. Nothing here extends `AbstractDomainObject` - if it did, it'd be an Extbase persisted entity and `Domain/Model` would be the right name; since nothing does, `Dto` is accurate.
- **`Utility/`** - pure, stateless helpers (mostly static methods, occasionally an instantiated zero-dependency class kept instantiable for testability, e.g. `ExtbaseFormFieldNamer`). Not for anything with a real service dependency in its constructor - that belongs in `Service/`.
- **`Command/`** - CLI commands and command-specific collaborators (e.g. `ComponentFileWriter`, only ever used by `ComponentAddCommand`). Keep those adjacent rather than promoting them to `Service/` - they're not reusable outside their one command.
- **`Registry/`** - process-lifetime singletons (`HydrationRegistry`, `PortalRegistry`) and their own collaborators.
- **`Traits/`** - behavior shared across multiple `Contexts/` classes; see Dependency Injection below for how they declare their host-class requirements.

### Dependency Injection

Every class under `Classes/` is auto-registered as an autowired TYPO3 service (`Configuration/Services.yaml`, `resource: '../Classes/*'`). Default to constructor injection for any collaborator that's part of this codebase - don't manually `new` it.

**When a class is already container-resolved with no extra constructor args** (a `Contexts/` class via `ComponentContextFactory::create()`'s `GeneralUtility::makeInstance($className)`, a Console `Command/` tagged `console.command`), just add the dependency as a normal typed constructor parameter - autowiring handles the rest.

**When an object needs both DI'd collaborators and a per-call runtime value that can't be autowired** (a dynamic class name, the caller's own `$this`), don't mix manual `new` with constructor injection, and don't bolt the runtime value on with a setter after construction either - a setter writing to a `readonly` property can't be proven by static analysis to run only once (`mago analyze` flags it as `possibly-invalid-property-write`), even where the real call graph guarantees it. Give the class a `Factory/` counterpart instead: the factory constructor-injects the DI'd collaborators (it holds no per-call state itself, so it stays safely container-shared) and its `create(...)` method takes the runtime value as a parameter, building the target object complete in one call - so the target class keeps a single, fully-promoted `readonly` constructor and never needs a setter. See `ComponentRendererFactory`, which builds `ComponentRenderer` (whose `componentResolver` is the calling `AbstractComponentCollection`'s own `$this`, not a generic service):

```php
#[Autoconfigure(public: true)]
final readonly class SomeFactory
{
    public function __construct(
        private SomeCollaborator $collaborator,
    ) {}

    public function create(SomeRuntimeValue $value): SomeTarget
    {
        return new SomeTarget($value, $this->collaborator);
    }
}
```

`GeneralUtility::makeInstance()` is still the right tool for:

- **Dynamic class-name resolution** - the target class is only known at runtime (e.g. resolving a Context subclass by name in `ComponentContextFactory`). A container can't autowire "whichever class this string names."
- **Static methods and Fluid ViewHelpers** - Fluid instantiates ViewHelpers itself, outside TYPO3's DI graph, so there's no `$this` to inject into; the same applies to any purely-static `Utility/` class.

Two `Symfony\Component\DependencyInjection\Attribute\Autoconfigure` flags matter here:

- **`public: true`** - required on any class fetched via `GeneralUtility::makeInstance()`/`$container->get()` from _outside_ the container's own constructor-graph wiring (an entry point: a dynamically-resolved `Contexts/` class, a `Factory/`). Not needed for a class only ever reached as another service's constructor-injected dependency.
- **`shared: false`** - needed when an object holds per-caller state bound at construction, so a cached singleton instance can't leak one caller's binding into another's.

### Strict Types

Always use strict types declaration:

```php
<?php

declare(strict_types=1);
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

### Narrowing `mixed` with `Typed::`

`AbstractComponentContext::get()` and ViewHelper `$this->arguments[...]` are untyped by nature - Fluid
template variables carry no static type. Use `Jramke\FluidPrimitives\Utility\Typed` (`int`/`intOrNull`,
`float`/`floatOrNull`, `string`/`stringOrNull`, `bool`/`boolOrNull`, `arrayOrNull`) to narrow them at the
call site instead of ad-hoc `is_string()`/cast checks, e.g. `Typed::stringOrNull($this->arguments['label'])`.

`Typed::bool()`/`boolOrNull()` only recognize explicit boolean keywords (`'true'/'1'/'yes'/'on'` etc.), not
PHP's general truthy coercion - using it on a `type="string"` prop (e.g. a CSS length like `"200px"`)
silently collapses to the default instead of testing truthiness. For that, cast the narrowed string instead:
`(bool)Typed::stringOrNull($x)`.

## Fluid Template Guidelines

### Props Definition

```html
<ui:prop name="variant" type="string" optional="{true}" default="primary" />
<ui:prop name="disabled" type="boolean" optional="{true}" client="{true}" />
```

### Ref Pattern (for hydration)

```html
<div
    {f:if(condition: class, then: 'class="{class}"')}
    {ui:ref(name: 'root', value: myOptionalValue, data: refData)}
    {ui:attributes()}>
    <f:slot />
</div>
```
