# Context

When building composable component parts we rely on a shared context so each sub-component can access information and props from a parent.

Each component has its own context that can be accessed via the `{context}` variable.

## Default Context

The default context exposes, among other things, the components `rootId`, `baseName` and **all props defined in the root component**.

## Context Flag

As mentioned you can also expose props from sub-components if you set `context={true}` in the `ui:prop` ViewHelper. You will mostly not need this as this prop is then also only available in children of that sub-component.

## External Contexts

You can also get context from other components using the `ui:context` ViewHelper. It takes a `name` prop which is the name `baseName` of any other component.

So in this example we could get the cards context from inside the dialog template.

```html
<ui:card.root>
    ...
    <ui:dialog.root> ... </ui:dialog.root>
</ui:card.root>
```

## Advanced Usage

By default the `context` object is a simple `BaseContext` class with all the context variables, that implements `ArrayAccess` and `Psr\Container\ContainerInterface` so you we can easily access context values in Fluid templates.

Sometimes you may need some additional logic inside your templates that require more then just accessing basic primitive props. In these cases, you can create your own context classes for each component by extending the `AbstractComponentContext` class and adding your custom logic there.

You need to name the context class the same as the component base name with a `Context` suffix and register the namespace of your context classes in your `ComponentCollection` implementation by overriding the `getContextNamespaces()` method.

```php
public function getContextNamespaces(): array
{
    return [
        'MyVendor\MyExt\Components\Contexts',
    ];
}
```

This is then automatically picked up by the `ComponentRenderer` when rendering components and the appropriate context class is instantiated and made available in the template as the `{context}` variable.

### Example

For example, if you have a component with the base name `Card`, you would create a context class named `CardContext` in the appropriate namespace.

```php
<?php

namespace MyVendor\MyExt\Components\Contexts;

use Jramke\FluidPrimitives\Contexts\AbstractComponentContext;

class CardContext extends AbstractComponentContext
{
    public function getSomething(): string
    {
        // $this->getRenderingContext()
        // $this->getParentRenderingContext()
        // $this->getAllVariables()
        // $this->get('someContextProp')
        return 'specialValue';
    }
}
```

This would make the `getSomething()` method available in your card's template via `{context.something}`. See Fluid's documentation on [arrays and objects](https://docs.typo3.org/permalink/fluid:variable-access-objects) for more information.

If you need methods with arguments you can use the [ui:call](../viewhelpers/call) ViewHelper to call methods on the context object.

### Exposing Client Props from Context

It is also possible to expose getter methods from your context class as client props by using the `#[ExposeToClient]` attribute.

```php
use Jramke\FluidPrimitives\Attributes\ExposeToClient;
class CardContext extends AbstractComponentContext
{
    #[ExposeToClient]
    public function getSomeValue(): mixed
    {
        return 'someCalculatedValue';
    }
}
```

This would add a `someValue` prop to the client props of the component with the value returned by the `getSomeValue()` method.
Optionally you can provide a name to override the prop name.

```php
    #[ExposeToClient(name: 'overriddenName')]
    public function getSomeValue(): mixed
    {
        return 'someCalculatedValue';
    }
```

### Dependency Injection

When you need to inject services into your context class, you can use constructor injection. Make sure you declare the class as a `public` service.

```php
#[Autoconfigure(public: true)]
class SomeContext extends AbstractComponentContext
{
    public function __construct(
        protected readonly PageRenderer $pageRenderer,
    ) {}
}
```

### Lifecycle Methods

If you need to perform some actions before or after rendering of the component, you can implement an `afterRendering` or `beforeRendering` method in your components context class. These methods will be called automatically by the `ComponentRenderer`.

{% component: "ui:alert.simple", arguments: {"title": "When modifying the ParentRenderingContext, make sure to clean it up in `afterRendering()`.", "variant": "warning"} %}

```php
public function beforeRendering(): void
{
    // Do something before rendering
}

public function afterRendering(string &$html): void
{
    // Do something after rendering
}
```
