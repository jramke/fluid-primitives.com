# Hydration

Fluid Primitives components render on the server, then "hydrate" on the client to add interactivity. Here's how that works.

## How It Works

1. **Server renders HTML.** Components output complete markup with data attributes
2. **Props are collected.** Client-side props are gathered into a hydration registry
3. **Script tag is injected.** Props are serialized to a `<script>` in the document head
4. **Client initializes.** JavaScript reads the props and attaches behavior

The result: fast initial paint, no layout shift, and progressive enhancement.

## Marking Elements for Hydration

Use `ui:ref` to connect DOM elements to their client-side counterparts:

```html
<!-- In Tooltip/Trigger.html -->
<button {ui:ref(name: 'trigger')}>
    <f:slot />
</button>
```

This outputs:

```html
<button data-scope="tooltip" data-part="trigger" data-hydrate-tooltip="rootId">Hover me</button>
```

The data attributes let the client find and connect elements to the state machine.

## Initializing Components

On the client, use `mount` to initialize components:

```typescript
import { mount } from 'fluid-primitives';
import { Accordion } from 'fluid-primitives/accordion';

mount('accordion', ({ props }) => {
    const accordion = new Accordion(props);
    accordion.init();
    return accordion;
});
```

This runs for every accordion on the page, extracting props from the hydration data and initializing each instance.

### Loading Scripts Per-Component

Include the initialization script in your component's root template:

```html
<!-- Accordion/Root.html -->
<primitives:accordion.root spreadProps="{true}">
    <f:slot />
</primitives:accordion.root>

<!-- Only loads when the component is used -->
<f:asset.script identifier="accordion" src="path/to/accordion.js" />
```

Or with Vite Asset Collector:

```html
<vite:asset entry="EXT:my_ext/Resources/Private/Components/Accordion/accordion.entry.ts" />
```

## Connecting Parts in JavaScript

When building custom components without using a state machine, use `ComponentHydrator` to find elements.
The `mount` callback provides a `createHydrator` function to create an instance that is automatically scoped to the current component instance:

```typescript
import { mount } from 'fluid-primitives';

mount('my-component', ({ props, createHydrator }) => {
    const hydrator = createHydrator();

    const triggers = hydrator.getElements('trigger');
    const content = hydrator.getElement('content');

    triggers.forEach(trigger => {
        trigger.addEventListener('click', () => {
            content.classList.toggle('open');
        });
    });
});
```

The built-in `Component` base class includes the `getElement` and `getElements` methods so you can skip creating a hydrator manually.

If you need to use `ComponentHydrator` outside of a `mount` callback, create an instance with the component name and root ID and optionally the `ids` mapping:

```typescript
import { ComponentHydrator } from 'fluid-primitives';
const hydrator = new ComponentHydrator('my-component', 'root-id-123');
```

## Controlled Components

By default, `mount` automatically initializes every component on the page. For components you want to control programmatically, set `controlled="{true}"`:

```html
<ui:collapsible.root controlled="{true}" rootId="my-collapsible"> ... </ui:collapsible.root>
```

This prevents automatic initialization. You then initialize manually:

```typescript
import { getHydrationData } from 'fluid-primitives';
import { Collapsible } from 'fluid-primitives/collapsible';

// Get props without auto-initialization
const props = getHydrationData('collapsible', 'my-collapsible');

const collapsible = new Collapsible({
    ...props,
    onOpenChange: ({ open }) => {
        console.log('Collapsible is now', open ? 'open' : 'closed');
    },
});
collapsible.init();
```

This is useful when:

- Building composite components that manage nested primitives
- Adding custom event handlers
- Integrating with external state management
- Conditionally initializing based on viewport or user action

## The Hydration Registry

Under the hood, a global `window.FluidPrimitives` object stores hydration data:

```javascript
window.FluidPrimitives = {
    hydrationData: {
        accordion: {
            'root-id-1': {
                controlled: false,
                props: {
                    id: 'root-id-1',
                    ids: [],
                    multiple: true,
                    defaultValue: ['item1']
                },
            },
            'root-id-2': { ... },
        },
        collapsible: { ... },
    },
    uncontrolledInstances: {
        // Initialized instances from mount()
    },
};
```

You rarely need to access this directly, but it's there for debugging or advanced use cases.
