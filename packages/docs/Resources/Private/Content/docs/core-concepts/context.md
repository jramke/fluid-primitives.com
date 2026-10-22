# Context

Context is how the parts of a component share data. Every part reads it as `{context}`. Native Fluid components have no counterpart.

## What's in context

A part finds these in `{context}`:

- `rootId` - the unique identifier of the component instance
- every prop of the root component
- `globals` - request-wide data shared by every component, see [Global Context](#content-global-context)

```html
<!-- ProductCard/Price.html -->
<p class="price">{context.currency} {context.price}</p>
```

## Exposing props to context

A part shares one of its own props with the parts nested in it when you mark the prop with `context="{true}"`:

```html
<!-- ProductCard/Variant.html -->
<ui:prop name="sku" type="string" context="{true}" />
```

The prop is then available as `{context.variant.sku}` in every part inside the variant. The key is the part's name.

## Accessing other components' context

To read the context of an enclosing component of another type, use `ui:context`. It takes the component's camelCase name and assigns the context to a variable:

```html
<ui:productCard.root price="19.90" currency="EUR">
    <ui:dialog.root>
        <!-- Inside the dialog, read the product card's context -->
        <ui:context name="productCard" as="cardContext" />
        <p>{cardContext.price} {cardContext.currency}</p>
    </ui:dialog.root>
</ui:productCard.root>
```

## Custom context classes

For custom logic, create a PHP context class. It keeps templates clean and the logic testable.

### Setup

1. Create a class extending `AbstractComponentContext`
2. Name and namespace it after the component's template folder (see below)
3. Register the base namespace in your component collection

```php
public function getContextNamespaces(): array
{
    return [
        'MyVendor\\MyExtension\\Components\\Contexts',
    ];
}
```

The class name is the component's name plus `Context`. The folders above the component become sub-namespaces of the registered namespace:

| Template                        | Context class                |
| ------------------------------- | ---------------------------- |
| `ProductCard/Root.html`         | `ProductCardContext`         |
| `Catalog/ProductCard/Root.html` | `Catalog\ProductCardContext` |

### Example

```php
namespace MyVendor\MyExtension\Components\Contexts;

use Jramke\FluidPrimitives\Contexts\AbstractComponentContext;

class ProductCardContext extends AbstractComponentContext
{
    public function getDiscountPercent(): ?int
    {
        $price = (float)$this->get('price');
        $discountPrice = $this->get('discountPrice');

        if ($discountPrice === null || $price <= 0) {
            return null;
        }

        return (int)round((1 - (float)$discountPrice / $price) * 100);
    }

    public function getStockState(array $variant): object
    {
        $stock = (int)($variant['stock'] ?? 0);

        return (object)[
            'inStock' => $stock > 0,
            'lowStock' => $stock > 0 && $stock <= 5,
        ];
    }
}
```

A getter without arguments reads like a variable. A method with arguments goes through `ui:call`:

```html
<f:if condition="{context.discountPercent}">
    <span class="badge">-{context.discountPercent}%</span>
</f:if>

<f:variable
    name="stock"
    value="{context -> ui:call(method: 'getStockState', arguments: {0: variant})}"
/>
```

### Available methods in context classes

```php
$this->get('propName');              // Get a context value
$this->getAllVariables();            // Get all context variables
$this->getRenderingContext();        // Current Fluid rendering context
$this->getParentRenderingContext();  // Parent component's rendering context
$this->getRequest();                 // Current HTTP request
```

## Exposing context to client

Mark a getter with `#[ExposeToClient]` to send its value to the client as a prop. A method must be public and take no required parameters, otherwise it is skipped:

```php
use Jramke\FluidPrimitives\Attributes\ExposeToClient;

class ProductCardContext extends AbstractComponentContext
{
    // Arrives in the client props as `discountPercent`
    #[ExposeToClient(excludeIfNull: true)]
    public function getDiscountPercent(): ?int
    {
        // ...
    }

    // Arrives as `currency`
    #[ExposeToClient(name: 'currency')]
    public function getCurrencyCode(): string
    {
        return 'EUR';
    }
}
```

## Lifecycle methods

Run code before or after a root component renders:

```php
class LightboxContext extends AbstractComponentContext
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

{% component: "ui:alert", arguments: {"title": "Cleanup Required", "text": "If `beforeRendering()` changes the parent rendering context, restore it in `afterRendering()` to keep the change local to this component.", "variant": "warning"} %}

## Dependency injection

Inject TYPO3 services into a context through its constructor. Add `#[Autoconfigure(public: true)]` so the component renderer can create the context with its services:

```php
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(public: true)]
class ProductCardContext extends AbstractComponentContext
{
    public function __construct(
        private readonly PriceFormatter $priceFormatter,
    ) {}

    public function getFormattedPrice(): string
    {
        return $this->priceFormatter->format((float)$this->get('price'));
    }
}
```

## Global context

Data that every component needs - design tokens, the page locale, icons - lives in the **global context**: one object per request that every component can read, the primitives of the library included. Native Fluid offers [`getAdditionalVariables()`](#content-additional-variables) on the component collection for this, which only serves the components of that collection and has no client side. The global context is the Fluid Primitives way. Read it as `{context.globals.<key>}` in a template and as `$this->getGlobals()` in a context class.

The library ships a default global context with two getters:

- `locale` - the language of the current site as a BCP 47 tag like `de-DE`, `null` when the render has no request, for example in a test
- `debug` - `true` in the Development application context

`getLocale()` is `final`, so your own global context adds getters and can override `getDebug()`, for example to turn the client's dev warnings on for a staging site. An override needs its own `#[ExposeToClient]`.

To add data, extend the library class and register it with `#[AsAlias]`:

```php
namespace MyVendor\MyExtension\Components\Contexts;

use Jramke\FluidPrimitives\Attributes\ExposeToClient;
use Jramke\FluidPrimitives\Contexts\GlobalContext as LibraryGlobalContext;
use Jramke\FluidPrimitives\Contexts\GlobalContextInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[AsAlias(GlobalContextInterface::class, public: true)]
final class GlobalContext extends LibraryGlobalContext
{
    // Read in templates and PHP: {context.globals.designTokens.colors.primary}
    public function getDesignTokens(): array
    {
        return ['colors' => ['primary' => '#0a7cff']];
    }
}
```

Like a component context, it is made of getters: `getDesignTokens()` is the key `designTokens`. A getter runs once per request, and the class can inject services through its constructor.

The sitepackage must [require `jramke/fluid-primitives`](/docs/getting-started#content-installation), otherwise your class could load before the library's and loses.

### Reading globals

```html
<time datetime="{date}" lang="{context.globals.locale}">...</time>

<div style="--accent: {context.globals.designTokens.colors.primary}">...</div>
```

In PHP, `get()` takes the same dot path. The first segment is a getter, the rest walks the returned array:

```php
$this->getGlobals()->get('locale');
$this->getGlobals()->get('designTokens.colors.primary');
```

Globals live under `context.globals`, separate from the data of the component, so a search for `context.globals` lists every use. The name `globals` is reserved: `getGlobals()` is `final` and a `<ui:prop name="globals">` is rejected.

### Exposing globals to the client

A getter marked `#[ExposeToClient]` on the global context is written once per page into `window.FluidPrimitives.globals`. The `name` can be a dot path, `myData.someValue` builds a nested object, and `excludeIfNull` works as on a component context. Read the values with [`getGlobal()`](/docs/client-utilities/globals), see also [Hydration](/docs/core-concepts/hydration#content-globals).

The library exposes `locale` and `debug` already.

{% component: "ui:alert", arguments: {"title": "Keep globals static", "text": "Globals end up in the cached page. Keep them the same for every visitor and small.", "variant": "warning"} %}

### Where does my data go?

| Need                                                           | Use                                                                                            |
| -------------------------------------------------------------- | ---------------------------------------------------------------------------------------------- |
| Variable in every component view, selected by component name   | `getAdditionalVariables()` on your component collection, see below                             |
| Project-wide data in every component, optionally on the client | a getter on your global context, `{context.globals.x}`, and `#[ExposeToClient]` for the client |
| Data of one component family with logic or services            | a getter on its `<Name>Context`, `{context.x}` inside that root                                |
| Per-instance client data                                       | `#[ExposeToClient]` on a component context getter, or `ui:prop client`                         |
| Constants for custom entry files                               | a global getter with `#[ExposeToClient(name: 'abc')]` and `getGlobal('abc')`                   |

### Additional variables

`getAdditionalVariables()` is the native Fluid hook to make a variable available in the views of components without a context class. Override it on your component collection. It receives the name of the component, so it can return different variables per component:

```php
public function getAdditionalVariables(string $viewHelperName): array
{
    return str_starts_with($viewHelperName, 'productCard') ? ['currency' => 'EUR'] : [];
}
```

```html
<span class="price">{price} {currency}</span>
```
