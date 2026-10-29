# Styling

Fluid Primitives ships no styles. Use the CSS approach your project already has: Tailwind, vanilla CSS, Sass or PostCSS.

`typo3 ui:add <component>` adds a component with basic Tailwind styles as a starting point. If your project uses another CSS approach, ask your AI to convert the classes to your CSS syntax.

## Theme tokens

The Tailwind classes of components added with `ui:add` use semantic color tokens (`bg-primary`, `text-muted-foreground`, `border-input`, ...). You can copy this example into your own stylesheet as a baseline.

```css
@import 'tailwindcss';

:root {
    --radius: 0.5rem;
}

@theme {
    --color-background: oklch(1 0 0);
    --color-foreground: oklch(0.141 0.005 285.823);
    --color-popover: oklch(1 0 0);
    --color-popover-foreground: var(--color-foreground);
    --color-primary: oklch(17.764% 0.00002 271.152);
    --color-primary-foreground: oklch(0.98 0.016 73.684);
    --color-secondary: oklch(95.213% 0.00011 271.152);
    --color-muted: oklch(0.967 0.001 286.375);
    --color-muted-foreground: oklch(0.552 0.016 285.938);
    --color-accent: oklch(0.967 0.001 286.375);
    --color-accent-foreground: oklch(0.21 0.006 285.885);
    --color-destructive: oklch(0.577 0.245 27.325);
    --color-border: oklch(0.92 0.004 286.32);
    --color-input: oklch(0.92 0.004 286.32);
    --color-ring: var(--color-primary);

    --animate-collapsible-down: collapsible-down 0.2s var(--ease-out);
    --animate-collapsible-up: collapsible-up 0.2s var(--ease-out);
    @keyframes collapsible-down {
        from {
            height: var(--collapsed-height, 0);
        }
        to {
            height: var(--height);
        }
    }
    @keyframes collapsible-up {
        from {
            height: var(--height);
        }
        to {
            height: var(--collapsed-height, 0);
        }
    }

    --animate-viewport-in: viewport-in 0.2s var(--ease-out);
    --animate-viewport-out: viewport-out 0.2s var(--ease-out) forwards;
    @keyframes viewport-in {
        from {
            opacity: 0;
            scale: 0.9;
        }
    }
    @keyframes viewport-out {
        to {
            opacity: 0;
            scale: 0.9;
        }
    }
}
```

- The `animate-collapsible-*` utilities drive [Collapsible](/docs/components/collapsible), whose content exposes `--height` and `--collapsed-height`.
- `animate-viewport-in` and `animate-viewport-out` fade and scale the [NavigationMenu](/docs/components/navigation-menu) viewport. Zag switches transitions off on the render that opens it, so both directions are animations.
- For a dark mode, override the same `--color-*` variables under your own dark selector.

## Data attributes

Every component part is marked with a single `data-<component>-<part>` attribute for precise targeting - the same convention Zag.js uses:

```html
<div data-accordion-item="«rootId»" data-state="open"></div>
```

The attribute's value is the component instance's root id, so target it with a presence selector like `[data-accordion-item]`. Interactive components also include state attributes like `data-state`, `data-disabled`, or `data-focus`.

## Styling with CSS

### Target by part

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

### Target by state

```css
[data-accordion-item][data-state='open'] {
    background-color: var(--color-surface-hover);
}

[data-accordion-item-trigger][data-disabled] {
    opacity: 0.5;
    cursor: not-allowed;
}
```

### Combine selectors

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
            Shipping
        </ui:accordion.trigger>
        <ui:accordion.content class="pb-4 text-gray-600">
            Orders ship within 2 working days.
        </ui:accordion.content>
    </ui:accordion.item>
</ui:accordion.root>
```

### State-based styling

Use Tailwind's data attribute variants:

```html
<ui:accordion.item class="border-b data-[state=open]:bg-gray-50">
    <ui:accordion.trigger class="py-4 data-disabled:opacity-50 data-disabled:cursor-not-allowed">
        ...
    </ui:accordion.trigger>
</ui:accordion.item>
```

### Target a part from its parent

The part attribute works as a Tailwind variant too:

```html
<ui:alert.root class="*:data-alert-content:text-sm">...</ui:alert.root>
```

### Common state variants

- `data-[state=open]:` - open accordion item, collapsible or dialog
- `data-[state=closed]:` - closed accordion item, collapsible or dialog
- `data-[selected]:` - selected tab
- `data-[state=checked]:` - selected item of a select
- `data-[disabled]:` - disabled part
- `data-[highlighted]:` - keyboard-highlighted item of a menu or select
