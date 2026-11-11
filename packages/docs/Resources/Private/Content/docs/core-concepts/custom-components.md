# Custom components

**Give a component of your own its behaviour in the browser, from a few lines in its entry file to a state machine.**

Your component renders on the server like any primitive: a `ui:prop` with `client="{true}"` for each value the browser needs, and [`ui:ref`](/docs/core-concepts/hydration#content-marking-elements-for-hydration) on the elements its script works with. How much structure the script gets is up to you.

## Add logic in the entry file

For an element that stays on the page and that other scripts leave alone, the [`mountAll`](/docs/client-utilities/mounting) callback is enough:

```typescript
import { mountAll } from 'fluid-primitives';

mountAll('ui:wishlistButton', ({ props, createHydrator }) => {
    const button = createHydrator().query('root');
    if (!button) return;

    let added = readWishlist().includes(props.productId);
    button.setAttribute('aria-pressed', String(added));

    button.addEventListener('click', () => {
        added = !added;
        button.setAttribute('aria-pressed', String(added));
        saveToWishlist(props.productId, added);
    });
});
```

`readWishlist()` and `saveToWishlist()` are the two `localStorage` helpers of the component below.

## Turn it into a component

The same wishlist button as a `Component` with a machine of one state. Its context holds `added`, the `TOGGLE` event changes it, and `render()` writes it to the button. Click it, then reload the page: the button remembers the product.

{% component: "ui:componentExample", arguments: { "componentName": "CustomComponentExamples.wishlist", "additionalFiles": {"WishlistButton.html": "EXT:docs/Resources/Private/Components/ui/WishlistButton/WishlistButton.fluid.html", "WishlistButton.entry.ts": "EXT:docs/Resources/Private/Components/ui/WishlistButton/WishlistButton.entry.ts"} } %}

`createMachine` comes from `@zag-js/core`, which Fluid Primitives depends on. Add it to your `package.json` in the same version.

Compared to the callback, the class gives you:

- One instance per root that other scripts find with `getComponentInstance('ui:wishlistButton', rootId)` and call, for example `api.toggle()`. `mount()` returns that instance when the root is mounted already.
- `destroy()`, which stops the machine and its effects. `destroyComponentsWithin()` calls it before you remove the markup, for example a row of a FieldArray.
- One place that writes the state to the DOM. `render()` runs after every change, so the attributes match the value at all times.
- `updateProps()` to change a prop at runtime.

## Model the states

When the component waits for something, such as a request, a permission or a timer, give each phase a state of its own. This store finder asks the browser for the location of the visitor. Click "Use my location", then try it again with location access blocked.

{% component: "ui:componentExample", arguments: { "componentName": "CustomComponentExamples.storeLocator", "additionalFiles": {"StoreLocator.html": "EXT:docs/Resources/Private/Components/ui/StoreLocator/StoreLocator.fluid.html", "StoreLocator.entry.ts": "EXT:docs/Resources/Private/Components/ui/StoreLocator/StoreLocator.entry.ts"} } %}

The root carries the state as `data-state`: `idle`, `locating`, `located` or `failed`. The template styles each one with `group-data-[state=locating]:` and the like, so you can design the states one by one, the way they are listed in the machine.

A state handles the events it lists. `locating` leaves `LOCATE` out, so a second click while the browser looks for the location is ignored, without a flag in your code. The effect `locate` runs while the machine is in `locating` and cleans up when it leaves. The list of states and events also makes a precise brief for whoever changes the component next, a colleague or an AI agent.

## Pick the shape

- **Logic in the entry file** - a small enhancement of markup that stays on the page.
- **A component with one state** - other scripts reach it, it gets removed and added again, or it holds a value it shows in several places.
- **A component with states** - it waits for a request, a permission or a timer, or each phase looks different.

## Component

{% component: "ui:apiReference", arguments: { "symbol": "Component" } %}

## Machine

{% component: "ui:apiReference", arguments: { "symbol": "Machine" } %}
