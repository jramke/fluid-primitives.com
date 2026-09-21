# Arguments

Components accept typed arguments called props. Fluid Primitives extends Fluid's built-in argument system with additional features.

## Defining Props

Use `ui:prop` instead of Fluid's `f:argument`:

```html
<ui:prop name="variant" type="string" default="primary" />
<ui:prop name="disabled" type="boolean" optional="{true}" />
<ui:prop name="items" type="array" />
```

See the [ui:prop ViewHelper reference](/docs/viewhelpers/prop) for all options.

### Enum Props

Some props use PHP backed enums. Until Fluid supports automatic enum conversion, pass enum cases with `f:constant`. This should get better once [PR #1271](https://github.com/TYPO3/Fluid/pull/1271) lands.

```html
<ui:tabs.root
    orientation="{f:constant(name: '
        Jramke\FluidPrimitives\Enum\Orientation::Horizontal
    ')}"
/>
```

## Automatic Props

Most components receive these props automatically. Availability depends on what a part actually renders - e.g. `class` and `asChild` only make sense on a part that renders its own wrapper element, so a part's own Arguments table (in its component docs page) is the source of truth for which of these it actually accepts.

### `class`

Pass additional CSS classes to any component:

```html
<ui:button class="mt-4 w-full">Submit</ui:button>
```

Inside your component, use `{class}` to apply them:

```html
<button class="btn {class}">
    <f:slot />
</button>
```

See [ui:cn ViewHelper](/docs/viewhelpers/cn) for conditional class names.

### `rootId`

Composable components get a unique identifier. This links parts together and enables hydration.

Usually auto-generated, but you can provide one:

```html
<ui:accordion.root rootId="faq-accordion"></ui:accordion.root>
```

See [Controlled Components](/docs/core-concepts/hydration#content-controlled-components).

### `asChild`

Merge attributes into child element instead of rendering the default wrapper. See [Composition](/docs/core-concepts/composition). Not available on root parts that render no wrapper element of their own (e.g. Dialog, Popover, Tooltip's `root`) - there's nothing to merge the attributes onto.

### `ids`

Override default IDs for component parts. Useful when composing multiple components together. See [Composition](/docs/core-concepts/composition).

### `controlled`

Mark a component as externally controlled, preventing automatic client-side initialization. See [Hydration](/docs/core-concepts/hydration#content-controlled-components).

### `attributes`

Forward additional HTML attributes. See [ui:attributes ViewHelper](/docs/viewhelpers/attributes).

## Client Props

Props needed for client-side behavior use `client="{true}"`:

```html
<ui:prop name="open" type="boolean" optional="{true}" client="{true}" />
```

These are serialized and passed to JavaScript during hydration. See [Client Prop Conversion](#content-client-prop-conversion) below if the client needs a different shape than what's serialized.

## Client Prop Conversion

A `client="{true}"` prop's serialized shape isn't always what its component's machine expects. PHP has no `File` objects or class instances to serialize, so some props go over the wire as plain JSON and need converting back on the client before the component is constructed.

Built-in primitives already do this for you: `Select`/`Combobox`'s `collection` is serialized as plain JSON and converted into a real `ListCollection`; `FileUpload`'s `translations` are serialized as `%fileName%`-placeholder strings and converted into the callback functions zag-js expects.

If you're authoring your own component the same way (see [Initializing Components](/docs/core-concepts/hydration#content-initializing-components)), register a converter at module scope with `registerClientPropConverter`, and reflect the converted shape in `HydrationPropsOverrides` so `props` stays typed correctly:

```typescript
import { registerClientPropConverter } from 'fluid-primitives';

registerClientPropConverter('myComponent', 'options', wireValue => {
    return new Map(Object.entries(wireValue as Record<string, unknown>));
});

declare module 'fluid-primitives/client' {
    interface HydrationPropsOverrides {
        myComponent: { options: Map<string, unknown> };
    }
}
```

`mountAll`/`mount` apply every registered converter to a hydration instance's props before constructing it, so `new MyComponent(props)` sees the converted shape directly - no cast needed between the wire shape and the machine shape.

## Inheriting Props

Use `ui:useProps` to inherit prop definitions from another component:

```html
<!-- Inherit all props from the primitive -->
<ui:useProps name="primitives:accordion.root" />

<!-- Inherit specific props only -->
<ui:useProps name="primitives:accordion.root" props="{0: 'multiple', 1: 'collapsible'}" />
```

This is how you build wrapper components without redefining every prop.
