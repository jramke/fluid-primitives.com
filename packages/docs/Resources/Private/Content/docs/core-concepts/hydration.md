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
<button data-scope="tooltip" data-part="trigger" id="tooltip:[rootId]:trigger">Hover me</button>
```

The data attributes and id let the client find and connect elements to the state machine.

### Repeated Parts Need a `value`

Without a `value:` argument, `ui:ref` generates the _same_ id every time that part name renders
within one component instance. That's correct for a true singleton part (root, trigger, content,
...), but if the same value-less part is placed more than once in one instance - most commonly a
purely decorative element with no data-driven identity of its own, like a separator between item
groups - every occurrence gets an identical, duplicate id:

```html
<!-- Bug: both separators below render id="menu:[rootId]:separator" -->
<div {ui:ref(name: 'separator')}></div>
```

Browsers don't warn about duplicate ids - it just makes `id`-based lookups (including this
library's own `getElement`/`getElementById`) silently resolve to whichever element happens to
match first. Give each occurrence its own value from [`ui:id`](../viewhelpers/id):

```html
<f:variable name="separatorId">{ui:id(prefix: 'separator')}</f:variable>
<div {ui:ref(name: 'separator', value: separatorId)}></div>
```

This applies whether you're building a first-party primitive or your own component with
`ui:ref` - ask yourself whether a part can legitimately appear more than once per instance, and if
so, whether it already has a natural per-item value (an item's own value, a tab's key) or needs
one generated this way.

If it slips through anyway, `warnAboutDuplicateIds()` scans the DOM for duplicate
fluid-primitives-managed ids and logs them to the console - it runs automatically whenever TYPO3's
own Application Context is `Development`, nothing to configure, and is a no-op otherwise so it
costs nothing in production.

## Initializing Components

On the client, use `mountAll` to initialize components:

```typescript
import { mountAll } from 'fluid-primitives';
import { Accordion } from 'fluid-primitives/accordion';

mountAll('accordion', ({ props }) => {
    const accordion = new Accordion(props);
    accordion.init();
    return accordion;
});
```

This runs for every accordion on the page, extracting props from the hydration data and initializing each instance. `props` here isn't an untyped bag - see [Typed Client Props](#content-typed-client-props) below for where that type comes from.

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

## Typed Client Props

`props` in a `mountAll`/`mount` callback isn't just whatever JSON happened to be on the page - it's a real, specific TypeScript type. Four things happen between a PHP prop value and the object your callback receives:

1. **PHP value becomes JSON.** Scalars and arrays serialize automatically; an object needs to say how.
2. **A TypeScript type is generated.** `ui:generate-hydration-types` turns every `client="{true}"` prop into a real type `mountAll`/`mount` infer from the component name alone.
3. **JSON reaches the browser.** Same script tag as [How It Works](#content-how-it-works) above - nothing extra for a typed prop specifically.
4. **JSON becomes the JS value your component expects.** Usually nothing more is needed, since JSON already is a JS value. When it isn't, `registerClientPropConverters` runs before your callback does.

Most props only ever touch steps 1 and 3. Steps 2 and 4 are where this gets interesting, and where the built-in primitives already show the pattern.

### 1. Getting a PHP Value to JSON

A scalar (`string`, `bool`, `int`, `float`) or a plain `array` prop is already JSON-valid, nothing to do. An object needs at least `JsonSerializable`:

```php
final class Rating implements JsonSerializable
{
    public function __construct(private int $value, private int $max = 5) {}

    public function jsonSerialize(): array
    {
        return ['value' => $this->value, 'max' => $this->max];
    }
}
```

That's enough for the page to render and hydrate correctly - the object serializes, the client gets plain JSON, done. It is _not_ enough for `ui:generate-hydration-types` to know what shape that JSON takes, though: without one of the two interfaces below, generating a type for this prop fails loudly at generation time rather than guessing.

### 2. Declaring the TypeScript Shape

Two interfaces answer "what does this object look like as JSON" - one for a class you own, one for a class you don't:

**`ClientTypeAwareInterface`** - implement it directly on your own class, alongside `JsonSerializable`:

```php
final class Rating implements JsonSerializable, ClientTypeAwareInterface
{
    // ...constructor and jsonSerialize() as above...

    public function getTsType(): string
    {
        return '{ value: number; max: number }';
    }

    public function getTsImport(): ?string
    {
        return null; // an inline literal - nothing to import
    }
}
```

`ListCollection` (`Classes/Domain/Dto/ListCollection.php`) is the real example: `Select` and `Combobox` both accept a `collection` prop, so instead of each generating its own duplicated inline literal, `ListCollection::getTsType()` points at one shared `ListCollectionData` type exported from `fluid-primitives/client`.

**`ClientPropConverterInterface`** - for a class you don't own (a vendor model, an Extbase model). Implement it as its own class; it's an ordinary autowired service, auto-discovered by tag, nothing else to register:

```php
final class FileReferenceConverter implements ClientPropConverterInterface
{
    public function supports(mixed $value, ArgumentDefinition $definition): bool
    {
        return $value instanceof FileReference;
    }

    public function convert(mixed $value): mixed
    {
        return ['url' => $value->getPublicUrl()]; // however you turn it into JSON-safe data
    }

    public function getTsType(): string
    {
        return '{ url: string }';
    }

    public function getTsImport(): ?string
    {
        return null;
    }
}
```

Match `supports()` narrowly (exact class/interface, not duck-typing) - it's checked against every client-marked object prop on the page.

Both interfaces are consulted at render time too, not just at codegen: an object prop matching neither, and not even `JsonSerializable`, throws immediately - scoped to that one component and prop - instead of silently breaking hydration for the entire page the way an uncaught JSON encoding failure otherwise would.

### 3. Generating the Type

```bash
typo3 ui:generate-hydration-types --collection='Your\Namespace\YourComponentCollection' --output=path/to/generated
```

This walks every root component your `ComponentCollectionInterface` knows about, reads its `client="{true}"` props (plus any `#[ExposeToClient]` context methods), and writes one `<Name>.hydration.ts` per component:

```typescript
// AUTO-GENERATED - do not edit by hand.
export type RatingHydrationProps = { id: string; ids: Record<string, string> } & {
    value: { value: number; max: number };
};

declare module 'fluid-primitives/client' {
    interface HydrationPropsRegistry {
        rating: RatingHydrationProps;
    }
}
```

That `declare module` block is what makes `mountAll('rating', ({ props }) => ...)` infer `props.value` as `{ value: number; max: number }`, no generic argument or cast needed anywhere. Built-in primitives get this from their own npm build automatically; `--collection`/`--output` point the same command at your own components (`--output` is only needed because your extension has no npm/tsdown pipeline of its own to re-export the generated file from the way this library does). Re-run it - with `--check` in CI, which exits non-zero on drift instead of writing - whenever a `client="{true}"` prop changes.

### 4. Converting JSON Back to a JS Value

JSON already _is_ a JS value, so most props stop here - the `Rating` type above is already exactly what your component can use. A converter is only for the cases where the wire shape and what your component's own API expects genuinely differ. `Select`'s `collection` is the real example: the wire shape is plain JSON (`{ items, itemToValueKey, ... }`), but `select.Props.collection` expects a real `ListCollection` _instance_, methods included:

```typescript
import { registerClientPropConverters } from 'fluid-primitives';

registerClientPropConverters('select', {
    collection: (collection: ListCollectionData) => getListCollectionFromHydrationData(collection),
});

declare module 'fluid-primitives/client' {
    interface HydrationPropsOverrides {
        select: { collection: select.Props['collection'] };
    }
}
```

Register this once, at module scope, in the same file as your component class - it runs before any `mountAll`/`mount` call, converting every matching prop before your callback ever sees it, so `new Select(props)` needs no cast between the wire shape and the machine shape. `FileUpload`'s `translations` is the other real example: zag-js expects `(file: File) => string` callbacks, but PHP has no `File` object to call a callback with, so `FileUploadContext` sends `%fileName%`-placeholder strings instead, and a registered converter wraps each one back into the function zag-js actually expects.

`HydrationPropsOverrides` is what keeps `props` typed correctly once a converter changes its shape: codegen (step 3) only ever knows the _wire_ shape, so wherever a converter runs, the override replaces that one key's generated type with the real one - both stay hand-written next to your component, never generated.

### End to End

A `tags` client prop that should arrive as a `Set<string>`, with no object or interface involved at all:

```html
<ui:prop name="tags" type="array" optional="{true}" client="{true}" />
```

```typescript
import { registerClientPropConverters } from 'fluid-primitives';

registerClientPropConverters('myComponent', {
    tags: wireValue => new Set(wireValue as string[]),
});

declare module 'fluid-primitives/client' {
    interface HydrationPropsOverrides {
        myComponent: { tags: Set<string> };
    }
}
```

`array` is already JSON-valid (step 1), codegen maps it to `string[]` with nothing to declare (step 2 only applies to objects), and the converter (step 4) does the rest. The docs site's own `CommandMenu` (`packages/docs/Resources/Private/Components/CommandMenu`) is a real, running example of the simpler end of this - a plain `string` client prop, generated via this same command against the docs site's own component collection, with no converter needed at all.

## Connecting Parts in JavaScript

When building custom components without using a state machine, use `ComponentHydrator` to find elements.
The `mountAll` callback provides a `createHydrator` function to create an instance that is automatically scoped to the current component instance:

```typescript
import { mountAll } from 'fluid-primitives';

mountAll('my-component', ({ props, createHydrator }) => {
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

If you need to use `ComponentHydrator` outside of a `mountAll` callback, create an instance with the component name and root ID and optionally the `ids` mapping:

```typescript
import { ComponentHydrator } from 'fluid-primitives';
const hydrator = new ComponentHydrator('my-component', 'root-id-123');
```

## Controlled Components

By default, `mountAll` automatically initializes every component on the page. For components you want to control programmatically, set `controlled="{true}"`:

```html
<ui:collapsible.root controlled="{true}" rootId="my-collapsible"> ... </ui:collapsible.root>
```

This prevents automatic initialization. You then initialize manually with `mount`, which targets one specific `rootId` regardless of its `controlled` flag:

```typescript
import { mount } from 'fluid-primitives';
import { Collapsible } from 'fluid-primitives/collapsible';

const collapsible = mount('collapsible', 'my-collapsible', ({ props }) => {
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

Unlike `mountAll`, `mount` doesn't track mounted state - calling it twice for the same `rootId` runs the callback twice, and instances created this way aren't picked up by `destroyComponentsWithin`.

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
        // Initialized instances from mountAll()
    },
};
```

You rarely need to access this directly, but it's there for debugging or advanced use cases.
