# Hydration

Hydration is the process of making server-rendered HTML interactive on the client and adding client-side behavior like event handling and state management.

## How it works

1. **Server renders HTML.** Components output complete markup with data attributes
2. **Props are collected.** Every component with a `ui:ref`, a client prop or `<ui:exposeToClient />` adds its data to a hydration registry
3. **Script tag is injected.** The registry is serialized into an inline `<script>` in the document head
4. **Client initializes.** Your entry file [mounts](/docs/client-utilities/mounting) the component: JavaScript reads the props and attaches behavior

## Marking elements for hydration

Add `ui:ref` to every element the client needs to find:

```html
<!-- ProductGallery/Thumbnail.html -->
<button {ui:ref(name: 'myThumbnail')}>
    <f:slot />
</button>
```

This outputs:

```html
<button data-product-gallery-my-thumbnail="[rootId]">Front view</button>
```

The attribute follows the convention of Zag.js for its own parts, `data-<component>-<part>="<rootId>"`. The client finds the element with `hydrator.query('myThumbnail')`, and CSS targets it with `[data-product-gallery-my-thumbnail]`. Zag.js adds the ids that ARIA links need when the component hydrates.

### Repeated parts

A part can appear several times in one instance, like the close buttons in the header and the footer of a dialog. Each occurrence carries the same attribute, and `hydrator.queryAll('closeTrigger')` returns all of them.

A part that repeats per item, such as an accordion item or a tab trigger, takes a `value`. It renders `data-value` too, which tells the client the items apart:

```html
<div {ui:ref(name: 'item', value: item.value)}>...</div>
<!-- <div data-accordion-item="[rootId]" data-value="a">...</div> -->
```

### Ids

When a part needs a specific id, declare it in the `ids` prop on the component's **root**, for the primitives and for your own components alike:

```html
<primitives:dialog.root ids="{content: 'cookie-settings-content'}"> ... </primitives:dialog.root>

<!-- your own component: every root component takes `ids` -->
<ui:lightbox ids="{content: 'lightbox-content'}" />
```

The keys are part names. `ui:ref` renders the id on the server, and the same map goes to the client, so the server-rendered id, the client lookup and the Zag.js machine agree. An `id` attribute on the element that carries `ui:ref` is unknown to the client, and Zag.js replaces it when it hydrates the part. `ids` applies to parts without a `value`.

## Loading scripts per component

Load the script from your component's root template, so pages without the component skip it:

```html
<!-- Accordion/Root.html -->
<ui:useProps name="primitives:accordion.root" as="rootProps" />

<primitives:accordion.root spreadProps="{rootProps}">
    <f:slot />
</primitives:accordion.root>

<f:asset.script identifier="accordion" src="path/to/accordion.js" />
```

Or with Vite Asset Collector:

```html
<vite:asset entry="EXT:my_extension/Resources/Private/Components/Accordion/accordion.entry.ts" />
```

Components added with `ui:add` already contain this line, with the path filled in for where they were written to (see [Scaffolding with the CLI](/docs/core-concepts/primitives#content-scaffolding-with-the-cli)).

## Mounting on the client

Your entry file mounts the components with [`mountAll`](/docs/client-utilities/mounting) and finds their parts with a [`ComponentHydrator`](/docs/client-utilities/component-hydrator).

## The hydration registry

The page carries its hydration data in `window.FluidPrimitives`, nested by Fluid namespace, component name and `rootId`, the same order as the `namespace:name` that `mountAll` and `mount` take:

```javascript
window.FluidPrimitives = {
    hydrationData: {
        primitives: {
            accordion: {
                'root-id-1': {
                    autoMount: true,
                    props: {
                        id: 'root-id-1',
                        ids: [],
                        multiple: true,
                        defaultValue: ['shipping']
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
    globals: {
        locale: 'de-DE',
        debug: false,
    },
};
```

Inspect it in the browser console to debug a component.

### Globals

`globals` holds the values of your [global context](/docs/core-concepts/context#content-global-context) that are marked `#[ExposeToClient]`, once per page and shared by all instances, plus `locale` and `debug` from the library. Read them with [`getGlobal()`](/docs/client-utilities/globals).

`window.FluidPrimitives` is written on pages where a component hydrates or a template reads `{context.globals.x}`. A read on the server emits all exposed globals. On other pages `getGlobals()` returns `null` and `getGlobal()` returns its fallback.
