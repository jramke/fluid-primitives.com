# Styling

Fluid Primitives is completely unstyled. Use any CSS approach you prefer: Tailwind, vanilla CSS, Sass, PostCSS or whatever you like.

When you add a component via the `typo3 ui:add <component>` command, you get basic Tailwind styles as a starting point. If you don't use Tailwind, simply ask your AI to convert these classes to your preferred CSS syntax.

## Data Attributes

Every component part is marked with a single `data-<component>-<part>` attribute for precise targeting - the same convention Zag.js uses:

```html
<div data-accordion-item="«rootId»" data-state="open"></div>
```

The attribute's value is the component instance's root id, so target it with a presence selector like `[data-accordion-item]`. Interactive components also include state attributes like `data-state`, `data-disabled`, or `data-focus`.

## Styling with CSS

### Target by Part

```css
[data-accordion-item-trigger] {
    display: flex;
    justify-content: space-between;
    padding: 1rem;
    font-weight: 500;
}

[data-accordion-item-content] {
    padding: 1rem;
    overflow: hidden;
}
```

### Target by State

```css
[data-accordion-item][data-state='open'] {
    background-color: var(--color-surface-hover);
}

[data-accordion-item-trigger][data-disabled] {
    opacity: 0.5;
    cursor: not-allowed;
}
```

### Combine Selectors

```css
[data-accordion-item-trigger]:hover:not([data-disabled]) {
    background-color: var(--color-surface-hover);
}

[data-accordion-item-trigger]:focus-visible {
    outline: 2px solid var(--color-focus-ring);
    outline-offset: 2px;
}
```

## Styling with Tailwind CSS

Pass classes directly to component parts:

```html
<ui:accordion.root>
    <ui:accordion.item class="border-b border-gray-200">
        <ui:accordion.trigger class="flex w-full justify-between py-4 font-medium hover:underline">
            Section Title
        </ui:accordion.trigger>
        <ui:accordion.content class="pb-4 text-gray-600"> Content goes here. </ui:accordion.content>
    </ui:accordion.item>
</ui:accordion.root>
```

### State-Based Styling

Use Tailwind's data attribute variants:

```html
<ui:accordion.item class="border-b data-[state=open]:bg-gray-50">
    <ui:accordion.trigger
        class="py-4 data-[disabled]:opacity-50 data-[disabled]:cursor-not-allowed"
    >
        ...
    </ui:accordion.trigger>
</ui:accordion.item>
```

### Target a Part from Its Parent

The part attribute works as a Tailwind variant too:

```html
<ui:alert.root class="*:data-alert-content:text-sm">...</ui:alert.root>
```

### Common State Variants

```html
data-[state=open]:...
<!-- Open state (accordion, collapsible, dialog) -->
data-[state=closed]:...
<!-- Closed state -->
data-[state=active]:...
<!-- Active tab -->
data-[disabled]:...
<!-- Disabled state -->
data-[highlighted]:...
<!-- Keyboard-highlighted item (menus, selects) -->
data-[selected]:...
<!-- Selected item -->
```
