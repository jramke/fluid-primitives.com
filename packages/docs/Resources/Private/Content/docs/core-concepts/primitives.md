# Primitives

Fluid Primitives provides headless, accessible primitives built on [Zag.js](https://zagjs.com/) state machines. They handle behavior and accessibility, you handle styling.

## What's a primitive?

A primitive is an unstyled component part with built-in:

- Keyboard navigation
- Focus management
- ARIA attributes
- State attributes like `data-state` and `data-disabled`

Primitives use the `primitives` namespace:

```html
<primitives:accordion.root>
    <primitives:accordion.item value="shipping">
        <primitives:accordion.trigger>Shipping</primitives:accordion.trigger>
        <primitives:accordion.content>
            Orders ship within 2 working days.
        </primitives:accordion.content>
    </primitives:accordion.item>
</primitives:accordion.root>
```

## Primitive vs component

**Primitives** (`primitives:*`) are the unstyled building blocks. Every part is its own tag, which gives you full control over the markup.

**Components** (`ui:*`) are your wrappers around primitives. They add your styling and can simplify the API, for example by combining parts.

A tooltip made of primitives:

```html
<primitives:tooltip.root>
    <primitives:tooltip.trigger />
    <primitives:tooltip.positioner>
        <primitives:tooltip.content>
            <primitives:tooltip.arrow />
        </primitives:tooltip.content>
    </primitives:tooltip.positioner>
</primitives:tooltip.root>
```

Your component can combine parts and provide an abstraction level that matches your design system:

```html
<ui:tooltip.root>
    <ui:tooltip.trigger />
    <ui:tooltip.content />
</ui:tooltip.root>
```

Of course its possible to use every other namespace for your own components as well.

## Building components from primitives

Wrap each primitive part in a template of your own. A tooltip needs three:

**`ui/Tooltip/Root.html`:**

```html
<ui:useProps name="primitives:tooltip.root" as="rootProps" />

<primitives:tooltip.root spreadProps="{rootProps}">
    <f:slot />
</primitives:tooltip.root>

<vite:asset entry="EXT:my_extension/Resources/Private/Components/ui/Tooltip/tooltip.entry.ts" />
```

**`ui/Tooltip/Trigger.html`:**

```html
<ui:useProps name="primitives:tooltip.trigger" as="triggerProps" />

<primitives:tooltip.trigger class="{class}" spreadProps="{triggerProps}">
    <f:slot />
</primitives:tooltip.trigger>
```

**`ui/Tooltip/Content.html`:**

```html
<ui:useProps name="primitives:tooltip.content" as="contentProps" />

<primitives:tooltip.positioner>
    <primitives:tooltip.content class="tooltip-content {class}" spreadProps="{contentProps}">
        <f:slot />
        <primitives:tooltip.arrow class="tooltip-arrow" />
    </primitives:tooltip.content>
</primitives:tooltip.positioner>
```

Use your styled, simplified API in a template:

```html
<ui:tooltip.root>
    <ui:tooltip.trigger>Delivery time</ui:tooltip.trigger>
    <ui:tooltip.content>Delivered in 2 to 3 working days</ui:tooltip.content>
</ui:tooltip.root>
```

## Key concepts

### `ui:useProps`

Imports prop definitions from another component, so your wrapper accepts the same props as the primitive without redeclaring them.

Set `as="rootProps"` when the wrapper renders the primitive (the common case above). It binds the imported, forwardable prop names to `{rootProps}` for `spreadProps`. Omit `as` to reuse just the prop definitions, see [Inheriting Props](/docs/core-concepts/arguments#content-inheriting-props).

```html
<!-- Import all props -->
<ui:useProps name="primitives:accordion.root" as="rootProps" />

<!-- Import specific props -->
<ui:useProps
    name="primitives:accordion.root"
    as="rootProps"
    props="{0: 'multiple', 1: 'defaultValue'}"
/>
```

### `spreadProps="{rootProps}"`

Forwards the props bound by the `as` of `ui:useProps` to the primitive in one call.

### JavaScript entry files

A primitive hydrates in the browser through an entry file. Load it in the root template, so a page includes it when it uses the component:

```html
<vite:asset entry="EXT:my_extension/Resources/Private/Components/ui/Tooltip/tooltip.entry.ts" />

<!-- Or with the core asset ViewHelper -->
<f:asset.script
    identifier="my-extension-tooltip"
    src="EXT:my_extension/Resources/Public/JavaScript/tooltip.js"
/>
```

## Scaffolding with the CLI

Use the TYPO3 CLI to scaffold components:

```bash
# List available primitives
typo3 ui:list

# Add a component to your project
typo3 ui:add accordion
```

This generates component files with basic Tailwind styling as a starting point.

The root template of every added component ends with a `<vite:asset>` line that loads its `.entry.ts` file, so the component hydrates without extra setup:

```html
<vite:asset entry="EXT:my_extension/Resources/Private/Components/ui/Accordion/Accordion.entry.ts" />
```

`ui:add` fills in the path the component is written to, following `--extension` and `--path`. The tag needs [praetorius/vite-asset-collector](https://packagist.org/packages/praetorius/vite-asset-collector) with the `vite` Fluid namespace registered, and a Vite config that picks up `Resources/Private/Components/**/*.entry.ts` as entrypoints. `ui:add` warns when the namespace is missing. If you load scripts differently, replace the line with your own, see [JavaScript Entry Files](#content-javascript-entry-files). Repeating the tag is fine, the asset is included once per page.

## Available primitives

Browse every primitive with examples and API documentation in [Components](/docs/components).

## Customize scaffolded components

The CLI scaffolding is a starting point for your own design system. Expect to:

- Customize styling to match your design
- Adjust the component structure where needed
- Adapt the JavaScript loading to your build setup

`ui:add` leaves components you already have untouched. If any of the component's files exists in the target folder, nothing is written and the command fails with the list of files in the way. Add `--force` to overwrite the whole component with the registry version, or move your customized component aside first. Other files in the folder stay as they are.

Once the code is in your project, it is yours: copy it, shape it, rename parts.
