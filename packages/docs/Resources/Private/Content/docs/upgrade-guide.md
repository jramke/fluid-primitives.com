# Upgrade to 1.0

The steps below cover the changes most projects run into. Every detail, with the betas listed one by one, is in the [changelog](/docs/releases).

{% component: "ui:alert", arguments: {"title": "Upgrade both packages together", "text": "The Composer and npm packages share one version. A 1.0 template with a 0.x client fails to hydrate.", "variant": "warning"} %}

## Update the packages

1.0 needs TYPO3 14, PHP 8.3 and `typo3fluid/fluid` 5.2 or newer.

```bash
composer require jramke/fluid-primitives:"^1.0@beta"
npm install fluid-primitives@beta
```

## Templates

### Name refs in camelCase

`ui:ref` names are camelCase, like part names everywhere else. The rendered attribute is `data-<component>-<part>` with the root id as its value:

```html
<!-- 0.x -->
<button {ui:ref(name: 'next-trigger')}>Next</button>

<!-- 1.0, renders data-carousel-next-trigger="«rootId»" -->
<button {ui:ref(name: 'nextTrigger')}>Next</button>
```

`ui:ref` renders no `data-scope` and `data-part`. Update CSS that selects your own parts by those attributes: `[data-scope=carousel][data-part=container]` becomes `[data-carousel-container]`.

A per-item value goes into its own `value` argument, which also renders `data-value`: `{ui:ref(name: 'item', value: item.value, data: refData)}`. See [ui:ref](/docs/viewhelpers/ref).

### Forward props with `as`

`ui:useProps` binds the imported props to a variable, and `spreadProps` takes that variable:

```html
<!-- 0.x -->
<ui:useProps name="primitives:select.root" />
<primitives:select.root spreadProps="{true}">...</primitives:select.root>

<!-- 1.0 -->
<ui:useProps name="primitives:select.root" as="rootProps" />
<primitives:select.root spreadProps="{rootProps}">...</primitives:select.root>
```

See [ui:useProps](/docs/viewhelpers/useProps).

### Mount a component yourself with `autoMount`

`controlled="{true}"` becomes `autoMount="{false}"`:

```html
<ui:dialog.root autoMount="{false}" rootId="cookie-settings">...</ui:dialog.root>
```

Mount it on the client with `mount()`, see [Mount on the client](#content-mount-on-the-client).

### Offer `asChild` in your own components

A component accepts `asChild` when its template marks the target tag with `{ui:asChild()}`. The primitives do this already. For your own components, add it next to `{ui:attributes()}`:

```html
<button {ui:attributes()} {ui:asChild()}>
    <f:slot />
</button>
```

See [Composition](/docs/core-concepts/composition#content-adding-aschild-to-your-own-component).

### Move constants for entry files to the global context

`ui:hydrationData` is gone. Expose constants like icons or labels with a getter on the [global context](/docs/core-concepts/context#content-global-context) and read them with [`getGlobal()`](/docs/client-utilities/globals). Static markup that only your script uses can also live in the entry file.

### Read the slot into a variable

`ui:slotFilled` is gone. Store the slot and test the variable, which renders the slot once:

```html
<f:variable name="note" value="{f:slot() -> f:spaceless()}" />
<f:if condition="{note}">
    <p class="text-sm">{note}</p>
</f:if>
```

### Pass the namespace to `ui:context`

`name` takes the component as you write its tag: `<ui:context name="ui:dialog" as="dialogContext" />`.

### Update the Select markup

- Wrap the items in the new `select.list` part, the element with `role="listbox"`. Move scroll styles such as `overflow-y-auto` from the content to the list. See [Select](/docs/components/select).
- `defaultValue` takes a string or a list of strings, and an integer throws. Pass a uid as a string, for example from a context getter. The same goes for Combobox.

### Rename the Field parts

- `field.description` is `field.helperText`.
- `field.error` is `field.errorText`.

See [Field](/docs/components/field).

### Mark HTML from `ui:call` as raw

`ui:call` escapes the text it returns. Wrap it in `f:format.raw` where the method returns HTML.

### Name templates `.fluid.html`

Recommended: rename component templates to `*.fluid.html`, like the primitives. `typo3 fluid:analyze` checks only files with that extension, which makes it a quick way to find templates that no longer parse.

## Client

### Mount on the client

| 0.x                                 | 1.0                                   |
| ----------------------------------- | ------------------------------------- |
| `mount('select', callback)`         | `mountAll('ui:select', callback)`     |
| `mountControlled('dialog', id, cb)` | `mount('ui:dialog', id, cb)`          |
| `getHydrationData('carousel', id)`  | `getHydrationData('ui:carousel', id)` |

Every lookup takes the namespaced name, as you write the tag. A wrapper like `ui:navigationMenu.root` registers under `ui:navigationMenu`, a primitive you use directly under `primitives:<name>`.

```typescript
import { mountAll } from 'fluid-primitives';
import { Select } from 'fluid-primitives/select';

mountAll('ui:select', ({ props }) => new Select(props).init());
```

Mount components with `mountAll()` or `mount()`, not with `new Select(getHydrationData(...).props)`. The mount functions convert client props such as a Select `collection` into the objects the machine expects, and the raw hydration data fails with `findMany is not a function`. See [Mounting](/docs/client-utilities/mounting).

### Find parts with the hydrator

- `hydrator.getElement(part)` is `hydrator.query(part)`, `getElements` is `queryAll`.
- Part names are camelCase: `hydrator.query('nextTrigger')`.
- `new ComponentHydrator(name, rootId, doc)` takes no `ids` argument. Inside a mount callback, `createHydrator()` builds one for you.
- In a component class, use `this.hydrator.query()` in place of `this.getElement()`.

See [ComponentHydrator](/docs/client-utilities/component-hydrator).

### Call `init()` on every component

The constructor stores the props, `init()` builds the hydrator, machine and api. Call `init()` before you read `api` or `hydrator`. A class that overrides `init()` returns `this`, so a mount callback can return `new Accordion(props).init()`.

## PHP

- `Jramke\FluidPrimitives\Domain\Model\ListCollection` is `Jramke\FluidPrimitives\Domain\Dto\ListCollection`. The client receives only value, label, disabled and group of each item. Read other keys from `item.original` in templates.
- `BaseContext` is `DefaultContext`, which is final and internal. Extend `AbstractComponentContext` for your own contexts. `globals` is a reserved prop name, and a context class cannot declare `getGlobals()`.
- `AjaxValidationTrait` is `JsonValidationErrorsTrait`. A form posts with [`extbase.post()`](/docs/client-utilities/extbase), and `onSubmit` receives no `post` helper. See [Forms](/docs/core-concepts/forms#content-send-the-values).
- Template errors throw `MissingContextException`, `MissingPropException` and `InvalidPropValueException`. Catch `Jramke\FluidPrimitives\Exception\ExceptionInterface`.

## Generate TypeScript types

Optional: [jramke/fluid-types](https://packagist.org/packages/jramke/fluid-types) types the `props` of `mountAll()` and `mount()` per component. See [Generating TypeScript types](/docs/core-concepts/arguments#content-generating-typescript-types).

```bash
composer require --dev jramke/fluid-types
vendor/bin/typo3 typescript:generate
```

Add the output to your `tsconfig.json` with a glob. A plain `"types.generated"` entry reads as a file name because of the dot, and TypeScript skips the folder:

```json
{
    "include": ["src", "types.generated/*.d.ts"]
}
```

## Check the result

- Run `typo3 fluid:analyze` and `tsc --noEmit`.
- Open each page type in the Development context. `devWarn()` messages start with `[fluid-primitives]` and point at a mount without hydration data or a component mounted twice.
