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
<button data-tooltip-trigger="[rootId]">Hover me</button>
```

It is the same single-attribute convention Zag.js uses for its own parts (`data-<component>-<part>="<rootId>"`). The client finds the element by it - `query('trigger')` - and CSS can target it with `[data-tooltip-trigger]`. No `id` is generated: Zag adds the ids it needs for ARIA links itself when the component hydrates.

### Repeated Parts

A part can appear more than once in one component instance - a dialog with a close button in the header _and_ one in the footer, say. That just works: every occurrence gets the same attribute, and `queryAll('closeTrigger')` returns all of them.

For parts that repeat _per item_ (accordion items, tab triggers), pass a `value` - it additionally renders `data-value`, which is how the client tells the items apart:

```html
<div {ui:ref(name: 'item', value: item.value)}>...</div>
<!-- <div data-accordion-item="[rootId]" data-value="a">...</div> -->
```

### Ids

Ids are only needed where an ARIA link points at an element, and Zag sets those when the component hydrates. When a part needs a specific id, declare it in the `ids` prop on the component's **root** - for the primitives and for your own components alike - and never as an `id` attribute on the element that carries `ui:ref`:

```html
<primitives:dialog.root ids="{content: 'my-dialog-content'}"> ... </primitives:dialog.root>

<!-- your own component: every root component takes `ids` -->
<ui:my-widget ids="{content: 'my-widget-content'}" />
```

The keys are part names. `ui:ref` renders that id on the server, and the same map is handed to the client, so the server-rendered id, the client lookup and Zag's own machine all agree. An `id` written directly on the element is unknown to the client and gets replaced when Zag hydrates the part. A part rendered with a `value` never gets an id from `ids`.

## Initializing Components

On the client, use `mountAll` to initialize components. The first argument is always
`"namespace:name"` - the Fluid namespace identifier your component collection is registered under
(see [Getting Started](../getting-started)), followed by the component's own name:

```typescript
import { mountAll } from 'fluid-primitives';
import { Accordion } from 'fluid-primitives/accordion';

mountAll('primitives:accordion', ({ props }) => {
    const accordion = new Accordion(props);
    accordion.init();
    return accordion;
});
```

This runs for every accordion on the page, extracting props from the hydration data and initializing each instance. Creating a component only stores its props - `init()` builds the state machine, renders and starts it, so `instance.machine` and `instance.api` are available once `init()` has run.

The namespace is required, not cosmetic: two different component collections can register a
same-named root component (e.g. your own styled wrapper around `primitives:accordion` that doesn't
forward every prop) - the hydration registry keeps them separate by namespace, and `mountAll`/`mount`
throw if you omit it, rather than guessing which one you mean.

### Loading Scripts Per-Component

Include the initialization script in your component's root template:

```html
<!-- Accordion/Root.html -->
<ui:useProps name="primitives:accordion.root" as="rootProps" />

<primitives:accordion.root spreadProps="{rootProps}">
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
The `mountAll` callback provides a `createHydrator` function to create an instance that is automatically scoped to the current component instance:

```typescript
import { mountAll } from 'fluid-primitives';

mountAll('ui:my-component', ({ props, createHydrator }) => {
    const hydrator = createHydrator();

    const triggers = hydrator.queryAll('trigger');
    const content = hydrator.query('content');

    triggers.forEach(trigger => {
        trigger.addEventListener('click', () => {
            content.classList.toggle('open');
        });
    });
});
```

`query()` returns the first matching element (or `null`), `queryAll()` every match. Both search the whole document by default, so content that was portaled elsewhere is found too - pass an element as the second argument to narrow the search to it.

The built-in `Component` base class includes the same `query` and `queryAll` methods so you can skip creating a hydrator manually. Elements you create on the client can be marked like `ui:ref` does with `hydrator.stamp(el, 'part', value?)`.

If you need to use `ComponentHydrator` outside of a `mountAll` callback, create an instance with the component name and root ID:

```typescript
import { ComponentHydrator } from 'fluid-primitives';
const hydrator = new ComponentHydrator('my-component', 'root-id-123');
```

Unlike `mountAll`/`mount`, `ComponentHydrator` itself takes the bare component name, not a
namespaced one - it only drives DOM-facing identity (the part attribute names), which stays the
same regardless of which namespace's collection rendered the component.

## Controlled Components

By default, `mountAll` automatically initializes every component on the page. For components you want to control programmatically, set `controlled="{true}"`:

```html
<ui:collapsible.root controlled="{true}" rootId="my-collapsible"> ... </ui:collapsible.root>
```

This prevents automatic initialization. You then initialize manually with `mount`, which targets one specific `rootId` regardless of its `controlled` flag:

```typescript
import { mount } from 'fluid-primitives';
import { Collapsible } from 'fluid-primitives/collapsible';

const collapsible = mount('ui:collapsible', 'my-collapsible', ({ props }) => {
    const instance = new Collapsible({
        ...props,
        onOpenChange: ({ open }) => {
            console.log('Collapsible is now', open ? 'open' : 'closed');
        },
    });
    instance.init();
    return instance;
});
```

Unlike `mountAll`, `mount` doesn't skip a `rootId` it's already seen - calling it twice re-runs the callback and constructs a new instance both times. The resulting instance is still tracked the same way a `mountAll`-created one is, though, so `getComponentInstance` and `destroyComponentsWithin` can find it.

This is useful when:

- Building composite components that manage nested primitives
- Adding custom event handlers
- Integrating with external state management
- Conditionally initializing based on viewport or user action

## The Hydration Registry

Under the hood, a global `window.FluidPrimitives` object stores hydration data, nested by Fluid
namespace first, then by component name, then by `rootId` - matching the `"namespace:name"` form
`mountAll`/`mount` themselves take:

```javascript
window.FluidPrimitives = {
    hydrationData: {
        primitives: {
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
        },
        ui: {
            collapsible: { ... },
        },
    },
    componentInstances: {
        // Instances from mountAll()/mount(), nested the same way
    },
};
```

You rarely need to access this directly, but it's there for debugging or advanced use cases.
