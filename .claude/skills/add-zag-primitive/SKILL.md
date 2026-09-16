---
name: add-zag-primitive
description: Port a Zag.js state machine into a new Fluid Primitives headless component - PHP context, Fluid templates, TS Component class, a styled /ui wrapper, and a docs page. Use when asked to implement zag-js/<component> as a new primitive, add a new headless component to fluid-primitives, or port a Zag.js machine into this library.
---

# Add a Zag.js-based primitive

Ports one Zag.js machine into this repo's three layers: the headless `fluid-primitives`
primitive (PHP context + Fluid templates + TS `Component` class), the styled `/ui` wrapper in
`packages/docs`, and its docs page. Read `CLAUDE.md` first (Context Class Pattern, Props
Definition, Ref Pattern, Dependency Injection) - this skill only covers what's specific to
*porting a Zag machine*, not the general PHP/TS/Fluid conventions already documented there.

Every input needed - machine source, prop/api types, CSS variables, data-attributes, keyboard behavior - is already installed
locally in `node_modules`. Read it from disk instead.

## Inputs

- **The component**, e.g. `slider`, `toggle-group`, `menu` - given by the user, as the skill
  argument or named in their message. Derive both forms: kebab-case package/prop-name (`toggle-group`)
  and PascalCase folder/class name (`ToggleGroup`).
- Optionally the user pastes extra context (docs prose, a design reference). Treat it as
  supplementary - the JSON data described below is normally sufficient on its own.

## Step 1 - Get the machine source locally

Check `node_modules/@zag-js/<component>` exists.

- **If missing**: add `"@zag-js/<component>": "^X.Y.Z"` to `packages/fluid-primitives/package.json`
  dependencies, matching the version already pinned on sibling `@zag-js/*` packages in that same
  file (e.g. `^1.44.0`). Then run `ddev npm install` from the repo root - `fluid-primitives` is
  consumed by the root package via `file:packages/fluid-primitives`, so root install resolves and
  hoists its deps into the top-level `node_modules`.
- The published package ships only `dist/` (no raw `src/`), but the `dist/*.mjs` files are
  **un-minified, readable ESM** - they *are* the source for this purpose. Read:
  - `dist/<component>.connect.mjs` - the `connect()` implementation. This is ground truth for
    exactly which attributes/data-attributes/aria-* each part returns. Cross-reference every
    server-rendered attribute against this file (see Step 4's rule).
  - `dist/<component>.types.d.ts` - `Props` interface (with doc comments, used for `ui:prop`
    descriptions) and `Api` interface (the client-side return shape).
  - `dist/<component>.anatomy.mjs` - the definitive list of anatomy parts
    (`data-scope`/`data-part` pairs). One Fluid template file per part.
  - `dist/<component>.props.mjs` - default prop values.
  - `dist/<component>.machine.mjs` - states/events/context, useful background, rarely needed for
    template work.

## Step 2 - Get structured docs data locally

The root `node_modules/@zag-js/docs/data/` directory ships the machine-readable equivalent of the
zagjs.com docs page, keyed by component slug, across four JSON files:

- `api.json` - `{ api: {...}, context: {...} }`: the public `Api` members and every `Props`
  field, each with `type` and `description`. Use `.context` to write accurate `ui:prop`
  descriptions.
- `data-attr.json` - per anatomy part: exact `data-scope`, `data-part`, `data-state` value union,
  and conditional attributes (`data-disabled`, `data-invalid`, etc.) with when they're present.
  This is the fastest cross-check for Step 4's attribute rule.
- `css-vars.json` - CSS custom properties the machine sets inline per part (e.g. Collapsible's
  `Content` part exposes `--height`/`--width`/`--collapsed-height`/`--collapsed-width`). Use these
  in the `/ui` wrapper's Tailwind classes/animations (Step 8).
- `accessibility.json` - keyboard interaction list. Use it for the docs page's Features/notes and
  to sanity-check which `aria-*`/`role` attributes matter.

Not every key exists in every file (some machines have no CSS vars, for instance) - that's normal,
just skip what's absent. If a component slug is missing from these files entirely (rare, only for
very new machines), fall back to reading `dist/<component>.connect.mjs` directly and, only then,
ask the user to paste the relevant docs prose rather than guessing.

## Step 3 - Study 1-2 structurally similar existing primitives

Under `packages/fluid-primitives/Resources/Private/Primitives/`, pick whichever existing
primitive(s) match the new component's shape and mirror their conventions exactly rather than
inventing new patterns:

- **Single-state toggle** (no sub-items): `Collapsible`, `Switch`
- **Item-collection with per-item state** (`getItemState(item)` on the context): `Accordion`,
  `RadioGroup`, `Tabs`
- **Popper-positioned overlay**: `Popover`, `Tooltip`
- **Portal-rendered overlay** (dialog/modal semantics): `Dialog`, `Popover`
- **Indicator/state-variant sub-parts** (`state="open"` two-sibling pattern): `Collapsible`'s
  `Indicator` + `CollapsibleIndicatorState` enum + `HasIndicatorStateTrait`

Read each one's full set of files together: `Classes/Contexts/<Name>Context.php`, every
`Resources/Private/Primitives/<Name>/*.fluid.html`, and `<Name>.ts`.

## Step 4 - PHP context class

`packages/fluid-primitives/Classes/Contexts/<Component>Context.php`, extends
`AbstractComponentContext`. Add a `getX()` method only for values that need real computation
(derived state strings, per-item state objects, style strings assembled from multiple props) -
per CLAUDE.md's Context Class Pattern. Narrow `$this->get(...)` with `Jramke\FluidPrimitives\Utility\Typed`.

- If a part needs an item-level state object (item-collection primitives), return `(object)[...]`
  from `getItemState(array $item)`, matching `AccordionContext`.
- If a part has a fixed set of state-variant siblings (open/closed-style indicators), add a
  backed enum under `Classes/Enum/` and reuse `Jramke\FluidPrimitives\Traits\HasIndicatorStateTrait`
  rather than re-implementing the "hidden unless matching state" check.
- Check `Classes/Traits/` for other shared logic (e.g. `HasCheckedStateDataAttributesTrait`) before
  writing new logic that another context already has.

## Step 5 - Fluid templates (one file per anatomy part) and the attribute rule

One `packages/fluid-primitives/Resources/Private/Primitives/<Component>/<Part>.fluid.html` per
part from `<component>.anatomy.mjs` (`Root.fluid.html`, `Trigger.fluid.html`, etc.). Follow
CLAUDE.md's Props Definition and Ref Pattern exactly (`<ui:prop>`, `{ui:ref(...)}`,
`{ui:attributes()}`). Mark a prop `client="{true}"` when the TS machine needs it at hydration
time; mark it `context="{true}"` when a context method needs it as a call argument (see
`Accordion/Item.fluid.html`'s `value`/`disabled` -> `getItemState`). Use
`{context -> ui:call(method: '...', arguments: {0: ...})}` for context methods that take
arguments; plain `{context.thing}` for zero-arg getters.

**Hard rule - never set an attribute the connect() function doesn't set for that part.** Check
`data-attr.json` and `<component>.connect.mjs` for the part's exact attribute name/casing before
adding it server-side. Don't invent attributes "for consistency" with other primitives if this
machine's `connect()` doesn't return them.

Within that rule, decide what to server-render per part like this - don't overcomplicate, but do
keep the ones that prevent an incorrect first paint:

- **Render server-side**: anything derivable from props alone at render time, where getting it
  wrong causes a visible flash before hydration - `role`, `type="button"`, `data-scope`/`data-part`
  (via `ui:ref`), `data-state`, `data-disabled`, `data-orientation`, and boolean `aria-*` states
  that follow directly from a default/controlled prop (`aria-expanded`, `aria-checked`,
  `aria-selected`, `aria-disabled`). Render `hidden` wherever its absence would flash unstyled
  content before hydration (see `Collapsible/Content.fluid.html`, `Collapsible/Indicator.fluid.html`).
- **Leave to the client**: anything needing a live DOM measurement, a generated id that
  cross-references another element (`aria-controls`, `aria-labelledby`, `aria-describedby` - the
  machine wires these itself via `dom.get<Part>Id(scope)`, don't hand-roll id generation in PHP),
  or event handlers. The TS `render()` method (Step 6) applies the *full* prop set from
  `api.get<Part>Props()` on hydration regardless - server attributes exist only to avoid an
  incorrect pre-hydration paint, not to duplicate the whole client API.
- When genuinely unsure whether a given attribute is safe to precompute from props alone, leave it
  client-only rather than guessing.

## Step 6 - TypeScript `Component` class

`packages/fluid-primitives/Resources/Private/Primitives/<Component>/<Component>.ts`, mirroring
`Collapsible.ts`: `import * as <component> from '@zag-js/<component>'`, extend
`Component<<component>.Props, <component>.Api>`, `static componentName = '<component>'`
(camelCase if the package name is multi-word, e.g. `toggleGroup`), `initMachine`, `initApi`,
`render()` spreading `api.get<Part>Props()` onto every ref'd element. Use `getElement`/`getElements`
for single/repeated parts and `spreadPropsByValue` for state-variant siblings (see `Collapsible.ts`'s
indicator handling).

No `.entry.ts` lives under `Primitives/` itself - entry files are only created in the docs `/ui`
wrapper (Step 8).

## Step 7 - Register the primitive

- `packages/fluid-primitives/tsdown.config.ts`: add `<component>: './Resources/Private/Primitives/<Component>/<Component>.ts'`
  to the entries map (alphabetical).
- `packages/fluid-primitives/package.json`: add the matching `"./<component>"` entry to `exports`
  (`import`/`types` pointing at `./dist/<component>.js` / `./dist/<component>.d.ts`), next to the
  `@zag-js/<component>` dependency added in Step 1.

## Step 8 - Functional test

`packages/fluid-primitives/tests/Functional/Components/<Component>RenderingTest.php`, following
`CollapsibleRenderingTest.php`'s shape: render minimal `<primitives:...>` markup via
`renderTemplate()` and assert on the concrete server-rendered output from Step 5 - `data-scope`/
`data-part`, the role/type, the aria-* and `hidden` attributes that differ across states (default
vs. explicit prop). Per CLAUDE.md's Test Quality Guidelines, don't test trivial getter passthrough -
test the state derivation logic actually written in the context class.

## Step 9 - The `/ui` wrapper in `packages/docs`

Under `packages/docs/Resources/Private/Components/ui/<Component>/`, mirror the `Collapsible`
folder exactly:

- One `<Part>.fluid.html` per anatomy part: `<ui:useProps name="primitives:<component>.<part>" />`
  then `<primitives:<component>.<part> spreadProps="{true}" class="{ui:cn(value: '... {class}')}">`.
  Apply Tailwind styling here (not in the headless primitive), driven by the `data-state`/
  `data-disabled`/etc. attributes from `data-attr.json` (e.g. `data-[state=open]:...`) and any CSS
  vars from `css-vars.json`.
- `<Component>.entry.ts`: `mountAll('<component>', ({ props }) => { const x = new <Component>(props); x.init(); return x; })`,
  importing `<Component>` from `fluid-primitives/<component>`.
- `Examples/<Case>.fluid.html` for each notable variant (simple usage, plus one per interesting
  prop combination - disabled, controlled, etc.), and `Examples/All.fluid.html` with an
  `<ui:prop name="example_id">` + `<f:switch>` dispatching to each, ending in `<ui:portalContainer />`.
- `<Component>.stories.ts`, one `StoryObj` per example id, matching the `Collapsible.stories.ts`
  shape.
- If the machine exposes CSS vars used for open/close or enter/exit animation, add the matching
  `@keyframes`/`--animate-*` pair to `packages/docs/Resources/Private/css/main.css` (see the
  `collapsible-down`/`collapsible-up` and `tooltip-in`/`tooltip-out` entries there) rather than
  inlining raw `@starting-style`/keyframe CSS in the component template.

## Step 10 - Docs page

`packages/docs/Resources/Private/Content/docs/components/<component>.md`, matching the structure
of `collapsible.md` exactly: H1 title + one-line bold description, `ui:referenceButtons`,
`ui:componentExample` for the primary example (`withEntryFile: true`), `## Features` (pull from
`accessibility.json`'s keyboard list plus anything notable from `api.json`), `## Installation`
(`ui:installationSection`), `## Examples` (one `### <Case>` + `ui:componentExample` per example
from Step 9), `## API Reference` (`ui:ComponentPropsTable` with a `[part, description]` pair per
anatomy part - the description should say what element it renders, e.g. "Renders a `<button>`
element"), `## Anatomy` (a fenced `html` block showing nested `<primitives:...>` tags, no
attributes). Then add `docs/components/<component>` to `packages/docs/Resources/Private/Content/nav.yaml`,
keeping the list alphabetically sorted.

## Verification

Run, from repo root, after all files are written:

```bash
ddev composer format
ddev composer run lint
ddev npm run types
ddev npm run format:check
ddev composer test:functional
```

Fix anything these surface before considering the primitive done.
