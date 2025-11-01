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

By default the `context` object is a simple `BaseContext` class that implements `ArrayAccess` and `Psr\Container\ContainerInterface` so you we can easily access context values in Fluid templates.

Sometimes you may need some additional logic inside your templates that require more logic then just accessing basic primitive props. In these cases, you can create your own context classes for each component by extending the `AbstractComponentContext` class and adding your custom logic there.

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
        // $this->renderingContext
        // $this->get('someContextProp')
        ...
        return 'specialValue';
    }
}
```

This would make the `getSomething()` method available in your card's template via `{context.something}`. See Fluid's documentation on [arrays and objects](https://docs.typo3.org/permalink/fluid:variable-access-objects) for more information.

If you need methods with arguments you can use the [ui:call](../viewhelpers/call) ViewHelper to call methods on the context object.
