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

`mount` creates a new instance on every call, so call `destroy()` on the old one before you mount the same `rootId` again. `getComponentInstance` and `destroyComponentsWithin` find an instance from `mount` like one from `mountAll`.

## mountAll

{% component: "ui:apiReference", arguments: { "symbol": "mountAll" } %}

## mount

{% component: "ui:apiReference", arguments: { "symbol": "mount" } %}

## getComponentInstance

{% component: "ui:apiReference", arguments: { "symbol": "getComponentInstance" } %}

## destroyComponentsWithin

{% component: "ui:apiReference", arguments: { "symbol": "destroyComponentsWithin" } %}
