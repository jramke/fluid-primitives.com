# ComponentHydrator

**Finds the parts of one component instance in the DOM.**

A component without a state machine finds its parts with a `ComponentHydrator`. The server marks the parts with [`ui:ref`](/docs/core-concepts/hydration#content-marking-elements-for-hydration), the hydrator queries them by name.

## Usage

The `mountAll` callback hands you `createHydrator`, which returns a hydrator scoped to the current instance:

```typescript
import { mountAll } from 'fluid-primitives';

mountAll('ui:productGallery', ({ createHydrator }) => {
    const hydrator = createHydrator();

    const thumbnails = hydrator.queryAll<HTMLButtonElement>('thumbnail');
    const image = hydrator.query<HTMLImageElement>('image');
    if (!image) return;

    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', () => {
            image.src = thumbnail.dataset.src ?? image.src;
        });
    });
});
```

`query()` returns the first matching element or `null`, `queryAll()` every match. Both search the whole document, so content that was portaled elsewhere is found too. Pass an element as the second argument to narrow the search to it.

Every `Component` instance exposes its own hydrator as `instance.hydrator`, for example `combobox.hydrator.query('list')`. Mark an element you create on the client like `ui:ref` does with `hydrator.stamp(el, 'part', value?)`.

Outside a `mountAll` callback, create a hydrator with the component name and the root id. It takes the bare component name, without a namespace:

```typescript
import { ComponentHydrator } from 'fluid-primitives';

const hydrator = new ComponentHydrator('productGallery', 'root-id-123');
```

## ComponentHydrator

{% component: "ui:apiReference", arguments: { "symbol": "ComponentHydrator" } %}
