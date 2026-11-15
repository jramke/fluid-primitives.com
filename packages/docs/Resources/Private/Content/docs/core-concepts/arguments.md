# Arguments

Props are the typed arguments of a component. You declare them with `ui:prop`, which works like Fluid's `f:argument` and can also send a prop to the client or share it with the other parts of a component.

## Defining props

Declare a prop with `ui:prop`. A prop is required unless it has a `default` or `optional="{true}"`:

```html
<ui:prop name="title" type="string" />
<ui:prop name="variant" type="string" default="outlined" />
<ui:prop name="featured" type="boolean" optional="{true}" />
```

See the [ui:prop ViewHelper reference](/docs/viewhelpers/prop) for all options.

## Automatic props

Components accept the props below without declaring them. Which ones a part takes depends on what it renders, and the arguments table on each component page lists them: `rootId`, `ids` and `autoMount` belong to root components, `class` to templates that use `{class}` and `asChild` to parts that opt in with `{ui:asChild()}`.

### `class`

Add CSS classes to a component:

```html
<ui:button class="mt-4 w-full">Add to cart</ui:button>
```

In your own component, `{class}` holds them:

```html
<button class="btn {class}">
    <f:slot />
</button>
```

See the [ui:cn ViewHelper](/docs/viewhelpers/cn) for conditional class names.

### `rootId`

Every root component has a `rootId` that ties its parts and its hydration data together. It is generated for you. Set your own when you need to find one specific instance, for example to [mount it manually](/docs/client-utilities/mounting#content-mount-one-instance-yourself):

```html
<ui:accordion.root rootId="faq-accordion"></ui:accordion.root>
```

### `asChild`

Merge a part's attributes into your own element instead of rendering the default wrapper, see [Composition](/docs/core-concepts/composition). It is available on parts that render their own wrapper element. The roots of Dialog, Popover and Tooltip render none. In your own component, add `{ui:asChild()}` to the tag that receives the attributes.

### `ids`

Declare the ids of a component's parts on its root, never on the individual parts:

```html
<ui:collapsible.root ids="{content: 'shipping-details-content'}"> ... </ui:collapsible.root>
```

The keys are part names. See [Composition](/docs/core-concepts/composition) and [Hydration](/docs/core-concepts/hydration#content-ids).

### `autoMount`

Set `autoMount="{false}"` to keep `mountAll` calls on the client from initializing a component, then mount it yourself with `mount`. Defaults to `true`. See [Mount one instance yourself](/docs/client-utilities/mounting#content-mount-one-instance-yourself).

### `attributes`

Forward additional HTML attributes to an element. The attributes that element sets itself stay as they are, see the [ui:attributes ViewHelper](/docs/viewhelpers/attributes#content-keep-what-the-element-sets-itself).

## Client props

Mark a prop of a root component with `client="{true}"` to send it to the client:

```html
<ui:prop name="defaultOpen" type="boolean" optional="{true}" client="{true}" />
```

The value is serialized into the hydration data and arrives as `props` in `mountAll`, see [Hydration](/docs/core-concepts/hydration). The optional [TypeScript package](/docs/core-concepts/typescript) types these `props` per component.

An object is sent as JSON. A class that implements `JsonSerializable` sends what `jsonSerialize()` returns, any other class all of its public properties, so keep secrets out of the public properties of an object you pass to a client prop.

## Inheriting props

Build a wrapper around a primitive with `ui:useProps`. It imports the prop definitions, so the wrapper accepts the primitive's props as they are:

```html
<!-- Inherit all props from the primitive -->
<ui:useProps name="primitives:accordion.root" as="rootProps" />

<!-- Inherit specific props -->
<ui:useProps
    name="primitives:accordion.root"
    as="rootProps"
    props="{0: 'multiple', 1: 'collapsible'}"
/>
```

`as="rootProps"` binds the imported, forwardable prop names under that name. Pass them on with `spreadProps="{rootProps}"` on the component you render:

```html
<primitives:accordion.root spreadProps="{rootProps}">
    <f:slot />
</primitives:accordion.root>
```

Add `as` whenever the wrapper renders the referenced component, it tells Fluid Primitives that the wrapper delegates. Without `as` the prop definitions are imported and nothing is forwarded, which suits a wrapper that reuses another component's props for its own markup.
