# Styling

Fluid Primitives is completely unstyled. Use any CSS approach you prefer: Tailwind, vanilla CSS, Sass, PostCSS or whatever you like.

When you add a component via the `typo3 ui:add <component>` command, you get basic Tailwind styles as a starting point. If you don't use Tailwind, simply ask your AI to convert these classes to your preferred CSS syntax.

## Theme Tokens for Registry Styles

The Tailwind classes of the registry components use semantic color tokens (`bg-primary`, `text-muted-foreground`, `border-input`, `ring-ring`, ...) instead of fixed colors, so a theme change is one place. Tailwind only generates these utilities when your CSS defines the tokens, so add this to the stylesheet that imports Tailwind and change the values to your design:

```css
@import 'tailwindcss';
@import 'tw-animate-css';

@custom-variant dark (&:where(.dark, .dark *));

:root {
    --radius: 0.5rem;
    --background: oklch(1 0 0);
    --foreground: oklch(0.141 0.005 285.823);
    --popover: oklch(1 0 0);
    --popover-foreground: var(--foreground);
    --primary: oklch(0.21 0.006 285.885);
    --primary-foreground: oklch(0.985 0 0);
    --secondary: oklch(0.967 0.001 286.375);
    --secondary-foreground: oklch(0.21 0.006 285.885);
    --muted: oklch(0.967 0.001 286.375);
    --muted-foreground: oklch(0.552 0.016 285.938);
    --accent: oklch(0.967 0.001 286.375);
    --accent-foreground: oklch(0.21 0.006 285.885);
    --destructive: oklch(0.577 0.245 27.325);
    --border: oklch(0.92 0.004 286.32);
    --input: oklch(0.92 0.004 286.32);
    --ring: oklch(0.705 0.015 286.067);
}

.dark {
    --background: oklch(0.141 0.005 285.823);
    --foreground: oklch(0.985 0 0);
    --popover: oklch(0.21 0.006 285.885);
    --popover-foreground: var(--foreground);
    --primary: oklch(0.92 0.004 286.32);
    --primary-foreground: oklch(0.21 0.006 285.885);
    --secondary: oklch(0.274 0.006 286.033);
    --secondary-foreground: oklch(0.985 0 0);
    --muted: oklch(0.274 0.006 286.033);
    --muted-foreground: oklch(0.705 0.015 286.067);
    --accent: oklch(0.274 0.006 286.033);
    --accent-foreground: oklch(0.985 0 0);
    --destructive: oklch(0.704 0.191 22.216);
    --border: oklch(1 0 0 / 10%);
    --input: oklch(1 0 0 / 15%);
    --ring: oklch(0.552 0.016 285.938);
}

@theme inline {
    --radius-sm: calc(var(--radius) - 4px);
    --radius-md: calc(var(--radius) - 2px);
    --radius-lg: var(--radius);

    --color-background: var(--background);
    --color-foreground: var(--foreground);
    --color-popover: var(--popover);
    --color-popover-foreground: var(--popover-foreground);
    --color-primary: var(--primary);
    --color-primary-foreground: var(--primary-foreground);
    --color-secondary: var(--secondary);
    --color-secondary-foreground: var(--secondary-foreground);
    --color-muted: var(--muted);
    --color-muted-foreground: var(--muted-foreground);
    --color-accent: var(--accent);
    --color-accent-foreground: var(--accent-foreground);
    --color-destructive: var(--destructive);
    --color-border: var(--border);
    --color-input: var(--input);
    --color-ring: var(--ring);

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
}
```

- `tw-animate-css` (`npm install -D tw-animate-css`) provides the `animate-in`, `fade-in` and `zoom-in-*` classes of Tooltip, Popover and the other floating components.
- The `animate-collapsible-*` utilities drive [Collapsible](/docs/components/collapsible), whose content exposes `--height` and `--collapsed-height`.
- The `.dark` block applies when an ancestor carries the `dark` class. Toggle it from your own theme switch, or drop the block and the `@custom-variant` line if you don't need dark mode.

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
