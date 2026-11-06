# Mounting

**Initializes the server-rendered components of a page on the client.**

`mountAll` mounts every instance of a component, `mount` one instance you pick by its `rootId`. Both read the props from the [hydration data](/docs/core-concepts/hydration#content-the-hydration-registry) the server wrote.

## Usage

Call `mountAll` in your entry file. Its first argument is `namespace:name`: the Fluid namespace your component collection is registered under (see [Getting Started](/docs/getting-started)), followed by the component's name:

```typescript
import { mountAll } from 'fluid-primitives';
import { Accordion } from 'fluid-primitives/accordion';

mountAll('primitives:accordion', ({ props }) => new Accordion(props).init());
```

`mountAll` calls your callback once per accordion on the page. `new Accordion(props)` stores the props. `init()` builds the state machine, renders and starts it, and returns the instance, so the callback can return it directly. Read `instance.machine`, `instance.api` and `instance.hydrator` after `init()`, an earlier read throws.

The namespace is required because two component collections can register a component with the same name, and `mountAll` and `mount` throw without it.

### Markup that arrives later

`mountAll` keeps watching the page. When markup with components arrives, for example a row of a FieldArray, a [`Template`](/docs/client-utilities/template) clone, or HTML that Turbo or a `fetch()` swapped in, it mounts them with the same callback, a microtask later. Hydration data blocks in the new HTML are read too. An instance is destroyed once every element it rendered onto has left the page, portaled content included, and stayed away for a task, so an element Turbo keeps with `data-turbo-permanent` keeps its state.

To have the instances on your next line, call `mountWithin` on the element that holds the new markup. To destroy instances right away, call `destroyComponentsWithin` on the element you are about to remove.

```typescript
import { mountWithin } from 'fluid-primitives';

list.append(clone);
mountWithin(clone.root);
```

A callback that returns no instance, one that only sets up other components for example, runs once per component. Return the instance, and it mounts again when its markup comes back. A component rendered with `autoMount="{false}"` mounts through `mount` only, also when its markup comes back.

A component mounts this way when the `mountAll` of its name already ran on the page and its hydration data is there. Load the entry file of every component the new HTML can contain. A Turbo Drive visit brings the data of the next page in its `<head>`. The server writes the data into the `<head>`, so the components in the HTML of a Turbo Frame or a `fetch()` mount when the page already has their data.

The hydration data of a page stays after its markup leaves, so a page Turbo restores from its cache mounts again. Give a `rootId` you set yourself to one component across all pages, or a restored page mounts with the props of the page that came last.

### Mount one instance yourself

`mountAll` mounts every instance on its own. To mount one yourself, for example to pass callbacks, set `autoMount="{false}"` on the root:

```html
<ui:collapsible.root autoMount="{false}" rootId="shipping-details"> ... </ui:collapsible.root>
```

Then call `mount` with the `rootId`. It mounts that instance whatever its `autoMount` value:

```typescript
import { mount } from 'fluid-primitives';
import { Collapsible } from 'fluid-primitives/collapsible';

const collapsible = mount('ui:collapsible', 'shipping-details', ({ props }) =>
    new Collapsible({
        ...props,
        onOpenChange: ({ open }) => {
            localStorage.setItem('shipping-details-open', String(open));
        },
    }).init()
);
```

When the `rootId` is mounted already, `mount` returns that instance and does not call your callback. So you can export the instance from a module, and a second run of that module still gets the one instance. To mount the `rootId` again, for example with other props, call `destroy()` on the instance first. `getComponentInstance` and `destroyComponentsWithin` find an instance from `mount` like one from `mountAll`.

## mountAll

{% component: "ui:apiReference", arguments: { "symbol": "mountAll" } %}

## mount

{% component: "ui:apiReference", arguments: { "symbol": "mount" } %}

## getComponentInstance

{% component: "ui:apiReference", arguments: { "symbol": "getComponentInstance" } %}

## mountWithin

{% component: "ui:apiReference", arguments: { "symbol": "mountWithin" } %}

## destroyComponentsWithin

{% component: "ui:apiReference", arguments: { "symbol": "destroyComponentsWithin" } %}
