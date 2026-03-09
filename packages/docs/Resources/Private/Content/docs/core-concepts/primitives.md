# Primitives

Fluid Primitives provides headless, accessible primitives built on [Zag.js](https://zagjs.com/) state machines. These handle behavior and accessibility; you handle styling.

## What's a Primitive?

A primitive is an unstyled component part with built-in:

- Keyboard navigation
- Focus management
- ARIA attributes
- State handling

Primitives use the `primitives` namespace:

```html
<primitives:accordion.root>
    <primitives:accordion.item value="item-1">
        <primitives:accordion.trigger>Section 1</primitives:accordion.trigger>
        <primitives:accordion.content>Content here</primitives:accordion.content>
    </primitives:accordion.item>
</primitives:accordion.root>
```

## Primitive vs Component

**Primitives** (`primitives:*`) are the raw building blocks. They're verbose but give you full control.

**Components** (`ui:*`) are your design system wrappers around primitives. They add your styling, simplify the API, and enforce consistency.

Example tooltip primitive anatomy:

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

Your simplified component might combine parts:

```html
<ui:tooltip.root>
    <ui:tooltip.trigger />
    <ui:tooltip.content />
    <!-- Wraps positioner + content + arrow -->
</ui:tooltip.root>
```

## Building Components from Primitives

Create wrapper components that use primitives internally. Here's a typical pattern:

**`ui/Tooltip/Root.html`:**

```html
<ui:useProps name="primitives:tooltip.root" />

<primitives:tooltip.root spreadProps="{true}">
    <f:slot />
</primitives:tooltip.root>

<vite:asset entry="EXT:my_ext/Resources/Private/Components/ui/Tooltip/tooltip.entry.ts" />
```

**`ui/Tooltip/Trigger.html`:**

```html
<ui:useProps name="primitives:tooltip.trigger" />

<primitives:tooltip.trigger class="{class}" spreadProps="{true}">
    <f:slot />
</primitives:tooltip.trigger>
```

**`ui/Tooltip/Content.html`:**

```html
<ui:useProps name="primitives:tooltip.content" />

<primitives:tooltip.positioner>
    <primitives:tooltip.content class="tooltip-content {class}" spreadProps="{true}">
        <f:slot />
        <primitives:tooltip.arrow class="tooltip-arrow" />
    </primitives:tooltip.content>
</primitives:tooltip.positioner>
```

Now you can use your styled, simplified API:

```html
<ui:tooltip.root>
    <ui:tooltip.trigger>Hover me</ui:tooltip.trigger>
    <ui:tooltip.content>Styled tooltip with arrow</ui:tooltip.content>
</ui:tooltip.root>
```

## Key Concepts

### `ui:useProps`

Imports prop definitions from another component. This ensures your wrapper accepts the same props as the primitive without redeclaring them.

### `spreadProps="{true}"`

Passes all inherited props from `ui:useProps` down to the primitive. Without this, you'd need to manually pass each prop.

```html
<!-- Import all props -->
<ui:useProps name="primitives:accordion.root" />

<!-- Import specific props -->
<ui:useProps name="primitives:accordion.root" props="{0: 'multiple', 1: 'defaultValue'}" />
```

### JavaScript Entry Files

Each primitive needs client-side initialization. Load it in the root template so it's only included when the component is used:

```html
<vite:asset entry="path/to/component.entry.ts" />

<!-- Or with core asset ViewHelper -->
<f:asset.script identifier="my-component" src="path/to/component.js" />
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

## Available Primitives

See the [Components](/docs/components) section for all available primitives with examples and API documentation.

## Registry Limitations

The CLI scaffolding provides a starting point, not a complete design system. You'll need to:

- Customize styling to match your design
- Possibly adjust the component structure
- Adapt the JavaScript loading to your build setup

It's inspired by [shadcn/ui](https://ui.shadcn.com/)'s approach: copy the code, own it, customize it.
