# Context

Composable components need to share data between parts. Context makes this possible without prop drilling.

Every component has a `{context}` variable containing shared state from its parent parts.

## What's in Context

By default, context includes:

- `rootId` - Unique identifier for the component instance
- `baseName` - The component's base name (e.g., "accordion", "dialog")
- All props defined on the root component
- `globals` - request-wide data shared by every component, see [Global Context](#content-global-context)

```html
<!-- In Accordion/Item.html -->
<div data-item-id="{context.rootId}">
    <!-- Access any prop from Accordion/Root.html -->
</div>
```

## Exposing Props to Context

Mark a prop to be available in child components with `context="{true}"`:

```html
<!-- In Accordion/Item.html -->
<ui:prop name="value" type="string" context="{true}" />
```

Now `{context.value}` is available in `Trigger.html` and `Content.html`.

## Accessing Other Components' Context

Need data from a sibling or ancestor component? Use `ui:context`:

```html
<ui:card.root>
    <ui:dialog.root>
        <!-- Inside the dialog, access the card's context -->
        <ui:context name="card" as="cardContext">
            <p>Card ID: {cardContext.rootId}</p>
        </ui:context>
    </ui:dialog.root>
</ui:card.root>
```

## Custom Context Classes

For complex logic, create a PHP context class. This keeps templates clean and logic testable.

### Setup

1. Create a class extending `AbstractComponentContext`
2. Name and namespace it after the component's own template folder (see below)
3. Register the base namespace in your `ComponentCollection`

```php
public function getContextNamespaces(): array
{
    return [
        'MyVendor\\MySitepackage\\Components\\Contexts',
    ];
}
```

**Naming**: the class name and its namespace mirror the component's own template path - the class
name is the component's own name, and everything above it in the path becomes a matching
sub-namespace under the registered base namespace. For a plain, non-nested root component this is
just `{ComponentName}Context` in the base namespace, e.g. `Accordion/Root.html` →
`AccordionContext`. For a component nested under a tier or example folder, the folder path carries
over one-to-one:

| Template                               | Context class                                     |
| -------------------------------------- | ------------------------------------------------- |
| `Accordion/Root.html`                  | `Contexts\AccordionContext`                       |
| `molecules/CheckboxGroup/Root.html`    | `Contexts\Molecules\CheckboxGroupContext`         |
| `CheckboxGroupExamples/SelectAll.html` | `Contexts\CheckboxGroupExamples\SelectAllContext` |

This is also what keeps two unrelated components from ever colliding on the same class just
because they share a last name segment - `Icon/Menu.html`'s context is
`Contexts\Icon\MenuContext`, never the real, unrelated `Contexts\MenuContext`.

### Example

```php
<?php

declare(strict_types=1);

namespace MyVendor\MySitepackage\Components\Contexts;

use Jramke\FluidPrimitives\Contexts\AbstractComponentContext;

class AccordionContext extends AbstractComponentContext
{
    public function getItemCount(): int
    {
        // Access context data
        $items = $this->get('items') ?? [];
        return count($items);
    }

    public function getItemState(array $item): object
    {
        $value = $item['value'] ?? null;
        $disabled = $item['disabled'] ?? null;

        $defaultValue = $this->get('defaultValue') ?? [];
        $rootDisabled = $this->get('disabled') ?? false;

        return (object)[
            'expanded' => in_array($value, (array)$defaultValue, true),
            'disabled' => $disabled ?? $rootDisabled,
        ];
    }
}
```

**In templates:**

```html
<!-- Simple getter (no arguments) -->
<span>Total items: {context.itemCount}</span>

<!-- Method with arguments - use ui:call -->
<f:variable name="itemState"
    >{context -> ui:call(method: 'getItemState', arguments: {0: itemProps})}</f:variable
>
```

### Available Methods in Context Classes

```php
$this->get('propName');              // Get a context value
$this->getAllVariables();            // Get all context variables
$this->getRenderingContext();        // Current Fluid rendering context
$this->getParentRenderingContext();  // Parent component's rendering context
$this->getRequest();                 // Current HTTP request
```

## Exposing Context to Client

Need computed values on the client side? Use the `#[ExposeToClient]` attribute:

```php
use Jramke\FluidPrimitives\Attributes\ExposeToClient;

class AccordionContext extends AbstractComponentContext
{
    #[ExposeToClient]
    public function getDefaultValue(): ?string
    {
        // This will be available in client-side props
        return $this->get('defaultOpen') ? $this->get('items')[0] : null;
    }

    #[ExposeToClient(name: 'customName')]
    public function getSomething(): mixed
    {
        // Available as 'customName' in props, not 'something'
        return 'value';
    }
}
```

## Lifecycle Methods

Run code before or after component rendering:

```php
class DialogContext extends AbstractComponentContext
{
    public function beforeRendering(): void
    {
        // Setup, modify parent context, etc.
    }

    public function afterRendering(string &$html): void
    {
        // Post-process HTML, cleanup, etc.
    }
}
```

{% component: "ui:alert", arguments: {"title": "Cleanup Required", "text": "If you modify the parent rendering context in beforeRendering(), clean it up in afterRendering() to avoid side effects.", "variant": "warning"} %}

## Dependency Injection

Inject TYPO3 services into context classes:

```php
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Core\Page\PageRenderer;

#[Autoconfigure(public: true)]
class MyContext extends AbstractComponentContext
{
    public function __construct(
        protected readonly PageRenderer $pageRenderer,
    ) {}
}
```

The `#[Autoconfigure(public: true)]` attribute is required for the component renderer to instantiate your context.

## Global Context

A component context holds the data of one component. Data that every component needs, like design tokens, feature flags, the page locale or icons, belongs in the **global context**: one object per request, reachable from every component, including the primitives of the library. Read it in a template as `{context.globals.<key>}` and in PHP as `$this->getGlobals()`.

The library ships a default global context with two getters:

- `locale` - the locale of the current site language, `null` when there is no request (a Storybook preview, a test)
- `debug` - `true` in the Development application context

Both are `final`: your own global context can extend them, not change them. To add data, extend the library class and mark it public for the container:

```php
<?php

declare(strict_types=1);

namespace MyVendor\MySitepackage\Components\Contexts;

use Jramke\FluidPrimitives\Attributes\ExposeToClient;
use Jramke\FluidPrimitives\Contexts\GlobalContext as LibraryGlobalContext;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[Autoconfigure(public: true)]
final class GlobalContext extends LibraryGlobalContext
{
    // Server only: {context.globals.designTokens.colors.primary}
    public function getDesignTokens(): array
    {
        return ['colors' => ['primary' => '#0a7cff']];
    }

    // Also sent to the client once per page: getGlobal('lightbox.icons')
    #[ExposeToClient(name: 'lightbox.icons')]
    public function getLightboxIcons(): array
    {
        $svg = static fn(string $name): string => (string)file_get_contents(
            GeneralUtility::getFileAbsFileName("EXT:my_sitepackage/Resources/Public/Svgs/{$name}.svg"),
        );

        return ['close' => $svg('lightbox-close'), 'next' => $svg('lightbox-arrow-next')];
    }
}
```

Like a component context, it is made of getters: `getDesignTokens()` is the key `designTokens`. Results are remembered for the rest of the request, and constructor injection works.

Register your class by aliasing the interface in your sitepackage's `Configuration/Services.yaml`. The library aliases it to its own class, and the extension loaded later wins, so there is always exactly one global context per project:

```yaml
Jramke\FluidPrimitives\Contexts\GlobalContextInterface:
    alias: MyVendor\MySitepackage\Components\Contexts\GlobalContext
    public: true
```

### Reading Globals

```html
<time datetime="{date}" lang="{context.globals.locale}">...</time>

<div style="--accent: {context.globals.designTokens.colors.primary}">...</div>
```

In PHP, `get()` takes the same dot path. The first segment is a getter, the rest walks the returned array:

```php
$this->getGlobals()->get('locale');
$this->getGlobals()->get('designTokens.colors.primary');
```

The access is explicit on purpose. There is no fallback from `{context.locale}` to the global context, so a lookup in a component context means what it always meant, and a global is easy to find with a search for `context.globals`.

`globals` is a reserved name: `getGlobals()` is `final` on `AbstractComponentContext`, so a context class that declares its own fails to load, and `<ui:prop name="globals">` is rejected.

### Exposing Globals to the Client

A getter marked `#[ExposeToClient]` on the global context is written once into `window.FluidPrimitives.globals`, not into the props of every component instance. The `name` may be a dot path, `lightbox.icons` builds a nested object, and `excludeIfNull` works as on a component context. Read the values with [`getGlobal()`](/docs/client-utilities/globals), see also [Hydration](/docs/core-concepts/hydration#content-globals).

`locale` and `debug` are exposed by the library, so `getGlobal('locale')` and `getGlobal('debug')` always work.

{% component: "ui:alert", arguments: {"title": "Static values only", "text": "Globals are part of the cached page. Do not put values in them that differ per user or per request, and keep the payload small: a handful of icons, not an icon library.", "variant": "warning"} %}

### Where Does My Data Go?

| Need                                                           | Use                                                                                                |
| -------------------------------------------------------------- | -------------------------------------------------------------------------------------------------- |
| Variable in every component view, selected by component name   | `getAdditionalVariables()` on your component collection, see below                                 |
| Project-wide data in every component, optionally on the client | a getter on your global context, `{context.globals.x}`, and `#[ExposeToClient]` for the client     |
| Data of one component family with logic or services            | a getter on its `<Name>Context`, `{context.x}` inside that root                                    |
| Per-instance client data                                       | `#[ExposeToClient]` on a component context getter, `ui:prop client`, `<ui:exposeToClient />`       |
| Constants for custom entry files                               | a global getter with `#[ExposeToClient(name: 'lightbox.icons')]` and `getGlobal('lightbox.icons')` |

### Additional Variables

To make a variable available in the views of components, without a context, override `getAdditionalVariables()` on your `ComponentCollection`. It receives the name of the component and can return different variables per component:

```php
public function getAdditionalVariables(string $viewHelperName): array
{
    return [
        'designTokens' => ['colors' => ['primary' => '#0a7cff']],
    ];
}
```

```html
<div style="color: {designTokens.colors.primary}">...</div>
```

### Default Context

A root component without a `<Name>Context` class of its own gets an internal default context. It carries no data and is not meant to be extended: put project-wide data in the global context and component-specific data in a `<Name>Context`.

{% component: "ui:alert", arguments: {"title": "Upgrading", "text": "The global context replaces `<ui:hydrationData>`, which is gone: expose constants for your entry files with a global getter and `getGlobal()`. A context class must not declare `getGlobals()` (it fails to load), and no component may have a prop named `globals`. `BaseContext` is now the internal `DefaultContext`; extend `AbstractComponentContext` instead.", "variant": "warning"} %}
