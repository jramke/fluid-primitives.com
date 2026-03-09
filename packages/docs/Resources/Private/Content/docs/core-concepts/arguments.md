# Arguments (Props)

Components accept typed arguments called props. Fluid Primitives extends Fluid's built-in argument system with additional features.

## Defining Props

Use `ui:prop` instead of Fluid's `f:argument`:

```html
<ui:prop name="variant" type="string" default="primary" />
<ui:prop name="disabled" type="boolean" optional="{true}" />
<ui:prop name="items" type="array" />
```

See the [ui:prop ViewHelper reference](/docs/viewhelpers/prop) for all options.

## Automatic Props

Every component receives these props automatically:

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

### `rootId`

Composable components get a unique identifier. This links parts together and enables hydration.

Usually auto-generated, but you can provide one:

```html
<ui:accordion.root rootId="faq-accordion"></ui:accordion.root>
```

See [Controlled Components](/docs/core-concepts/hydration#content-controlled-components).

### `asChild`

Merge attributes into child element instead of rendering the default wrapper. See [Composition](/docs/core-concepts/composition).

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

These are serialized and passed to JavaScript during hydration.

## Inheriting Props

Use `ui:useProps` to inherit prop definitions from another component:

```html
<!-- Inherit all props from the primitive -->
<ui:useProps name="primitives:accordion.root" />

<!-- Inherit specific props only -->
<ui:useProps name="primitives:accordion.root" props="{0: 'multiple', 1: 'collapsible'}" />
```

This is how you build wrapper components without redefining every prop.
