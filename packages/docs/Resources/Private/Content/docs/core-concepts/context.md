# Context

Composable components need to share data between parts. Context makes this possible without prop drilling.

Every component has a `{context}` variable containing shared state from its parent parts.

## What's in Context

By default, context includes:

- `rootId` - Unique identifier for the component instance
- `baseName` - The component's base name (e.g., "accordion", "dialog")
- All props defined on the root component

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
2. Name it `{ComponentName}Context` (e.g., `AccordionContext`)
3. Register the namespace in your `ComponentCollection`

```php
public function getContextNamespaces(): array
{
    return [
        'MyVendor\\MySitepackage\\Components\\Contexts',
    ];
}
```

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
<f:variable name="itemState">{context -> ui:call(method: 'getItemState', arguments: {0: itemProps})}</f:variable>
```

### Available Methods in Context Classes

```php
$this->get('propName');              // Get a context value
$this->getAllVariables();            // Get all context variables
$this->getRenderingContext();        // Current Fluid rendering context
$this->getParentRenderingContext();  // Parent component's rendering context
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
