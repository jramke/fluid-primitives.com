# Portal Select & Combobox content by default

**Status:** Not started

**Note:** breaking changes are fine — this library is pre-v0/v1 with no external compatibility contract yet. Don't add back-compat shims, deprecated fallbacks, or feature flags to soften the fix below; just make the clean change.

## Goal

Wrap Select's and Combobox's `Content` part in `<ui:portal>` unconditionally, matching Popover/Dialog/Tooltip. No new prop — this becomes the default with no opt-out, mirroring the existing pattern exactly.

## How portal already works (Popover/Tooltip/Dialog)

- `<ui:portal>` ([PortalViewHelper.php](../../packages/fluid-primitives/Classes/ViewHelpers/PortalViewHelper.php)) buffers its rendered children into a static [PortalRegistry](../../packages/fluid-primitives/Classes/Registry/PortalRegistry.php) instead of outputting them inline.
- `<ui:portalContainer />` ([PortalContainerViewHelper.php](../../packages/fluid-primitives/Classes/ViewHelpers/PortalContainerViewHelper.php)) flushes the buffer wherever it's placed — once, globally, in [Default.html:5](../../packages/docs/Resources/Private/PageView/Layouts/Default.html).
- Usage is hardcoded directly in each `Content.html`, e.g. [Registry/Popover/Content.html](../../packages/docs/Resources/Private/Registry/Popover/Content.html):
    ```html
    <ui:portal>
        <primitives:popover.positioner ...>
            <primitives:popover.content spreadProps="{true}" class="...">...</primitives:popover.content>
        </primitives:popover.positioner>
    </ui:portal>
    ```
- `ComponentRenderer.php:353-358` already snapshots/diffs `PortalRegistry` around root-component rendering to detect hydration refs that live entirely behind a portal — this generic machinery needs **no changes** to support Select/Combobox.

## Current state of Select/Combobox

No portal anywhere (`grep -rni portal` across both trees returns nothing). `Content.html` renders inline, nested inside `Root`'s wrapper `<div>`:

- [Registry/Select/Content.html](../../packages/docs/Resources/Private/Registry/Select/Content.html)
- [Registry/Combobox/Content.html](../../packages/docs/Resources/Private/Registry/Combobox/Content.html)

## The blocker (must fix first, or interactivity silently breaks)

Unlike Dialog/Popover/Tooltip's `Root` (which renders _no_ wrapping element — just `<f:slot />`), Select's and Combobox's `Root` **does** render a real `<div {ui:ref(name:'root', ...)}>` (needed to group Label/Control/Content and carry `invalid`/`readonly` state — see [Primitives/Select/Root.html](../../packages/fluid-primitives/Resources/Private/Primitives/Select/Root.html)).

Client-side hydration in [hydration.ts](../../packages/fluid-primitives/Resources/Private/Client/src/lib/hydration.ts):

```ts
getElements<T extends Element>(part: string, parent: Element | Document = this.doc): T[] {
    const isDoc = parent === this.doc;
    const searchScope: Element | Document = isDoc
        ? this.getElement('root') || this.doc   // <-- scopes to inside #root when it exists
        : parent;
    ...
}
```

`getElements()` (plural — used for `itemGroup`, `item`, `itemText`, `itemIndicator`) defaults to searching **inside the `root` element's subtree** whenever a `root` element exists, only falling back to whole-document when it doesn't. Popover/Dialog/Tooltip get portal-safety "for free" because their `root` never exists as an element, so every plural lookup already falls back to document-wide search. Select/Combobox's `root` **does** exist, so once `Content` is portalled out from under it, calls like `this.getElements('itemGroup')` in [Select.ts:101](../../packages/fluid-primitives/Resources/Private/Primitives/Select/Select.ts#L101) and [Combobox.ts:286,330](../../packages/fluid-primitives/Resources/Private/Primitives/Combobox/Combobox.ts#L286) (plus the `spreadPropsByValue('item'|'itemText'|'itemIndicator', ...)` calls at [Select.ts:116-129](../../packages/fluid-primitives/Resources/Private/Primitives/Select/Select.ts#L116) / [Combobox.ts:310-328](../../packages/fluid-primitives/Resources/Private/Primitives/Combobox/Combobox.ts#L310)) would silently return zero matches. Options would never get their Zag-generated `role`/`aria-selected`/event attributes — no error thrown, just broken interactivity.

Note: `spreadPropsByValue`/`spreadPropsByOptionalValue` in [component.ts:98-121](../../packages/fluid-primitives/Resources/Private/Client/src/lib/component.ts#L98) already forward an optional `{ parent }` through to `getElements`, so the plumbing for an explicit scope already exists — passing `this.doc` explicitly does **not** help today, though: `isDoc = parent === this.doc` would still be `true` and re-trigger the root-scoping. A parent must be something other than the literal `this.doc` reference to take the `searchScope = parent` branch. That's exactly what the fix below addresses.

## Recommended fix — explicit opt-out per call site, not a retry

The root cause is that `parent: Element | Document = this.doc` makes "not passed" and "explicitly passed `this.doc`" indistinguishable (`isDoc = parent === this.doc` is `true` either way), so there's currently no way to opt out of the root-scoping shortcut at all. Fix that by making `parent` genuinely optional and only falling back to the current root-then-doc chain when it's actually omitted:

```ts
getElements<T extends Element>(part: string, parent?: Element | Document): T[] {
    const searchScope: Element | Document = parent !== undefined
        ? parent                                    // caller knows exactly where to look
        : (this.getElement('root') || this.doc);    // unchanged default chain: root, else whole doc

    const dataPart = toKebabCase(part);
    const escapedPartId = CSS.escape(this.computePartId(part));
    const escapedValueSeparator = CSS.escape(this.getValueSeparatorForPart(part));

    return Array.from(
        searchScope.querySelectorAll<T>(
            `[id="${escapedPartId}"][data-part="${dataPart}"],[id^="${escapedPartId + escapedValueSeparator}"][data-part="${dataPart}"]`
        )
    );
}
```

(`Component.getElements()` in [component.ts:86](../../packages/fluid-primitives/Resources/Private/Client/src/lib/component.ts#L86) already declares `parent?: HTMLElement | Document` and forwards it through untouched, so no change needed there or in `spreadPropsByValue`/`spreadPropsByOptionalValue` — they already accept and forward `options?.parent`.)

Then, at the specific Select/Combobox call sites that need to search past a portalled `root`, explicitly pass `this.doc` (already a public/accessible property on `Component`, see [component.ts:32](../../packages/fluid-primitives/Resources/Private/Client/src/lib/component.ts#L32) — `Select`/`Combobox` extend `Component` so `this.doc` is already available):

- [Select.ts:101](../../packages/fluid-primitives/Resources/Private/Primitives/Select/Select.ts#L101): `this.getElements('itemGroup', this.doc)`
- [Select.ts:116,121,126](../../packages/fluid-primitives/Resources/Private/Primitives/Select/Select.ts#L116): add `{ parent: this.doc }` as the third argument to each `spreadPropsByValue('item'|'itemText'|'itemIndicator', ...)` call
- [Combobox.ts:286](../../packages/fluid-primitives/Resources/Private/Primitives/Combobox/Combobox.ts#L286): `this.getElements('itemGroup', this.doc)`
- [Combobox.ts:310,320,325](../../packages/fluid-primitives/Resources/Private/Primitives/Combobox/Combobox.ts#L310): add `{ parent: this.doc }` to each `spreadPropsByValue(...)` call
- [Combobox.ts:330](../../packages/fluid-primitives/Resources/Private/Primitives/Combobox/Combobox.ts#L330): `this.getElements('item', this.doc)`
- [Combobox.ts:333](../../packages/fluid-primitives/Resources/Private/Primitives/Combobox/Combobox.ts#L333) (`getElements('item', itemGroupEl)`) needs no change — it already passes a concrete element as `parent`, so it's unaffected either way.

Every other component's `getElements()` calls that rely on the implicit default (omit `parent` entirely) keep today's exact behavior — this only changes what's possible when a caller opts in with an explicit `parent`. Re-verify line numbers during implementation since files may have shifted since this plan was written.

This is a breaking change to `getElements()`'s default parameter (removing `= this.doc`) — that's fine per the note at the top of this doc.

## Then, the actual portal wrap

Apply to both the registry (distributable) and consumer/docs copies, matching Popover's exact shape:

- `packages/docs/Resources/Private/Registry/Select/Content.html`
- `packages/docs/Resources/Private/Components/ui/Select/Content.html`
- `packages/docs/Resources/Private/Registry/Combobox/Content.html`
- `packages/docs/Resources/Private/Components/ui/Combobox/Content.html`

Just wrap the existing `<primitives:select.positioner>...</primitives:select.positioner>` (and combobox equivalent) block in `<ui:portal>...</ui:portal>` — no other markup changes.

## Things to double-check (not expected to need code changes)

- `<ui:portalContainer />` placement: already present once globally in [Default.html:5](../../packages/docs/Resources/Private/PageView/Layouts/Default.html), and already present (pre-emptively/stray) in [Select/Examples/All.html](../../packages/docs/Resources/Private/Components/ui/Select/Examples/All.html) and [Combobox/Examples/All.html](../../packages/docs/Resources/Private/Components/ui/Combobox/Examples/All.html). Check the _other_ Storybook example files for Select/Combobox (`Simple.html`, `Multiple.html`, `WithField.html`, etc.) actually render inside a layout/story wrapper that includes a portal container — add one if any standalone example doesn't.
- `Positioner.html`'s no-JS fallback style (`style="{settings.defaultFloatingStyles}"`, defined in [setup.typoscript:4](../../packages/fluid-primitives/Configuration/Sets/Default/setup.typoscript#L4) as `position: absolute; isolation: isolate; top: 0; left: 0;`) shouldn't be affected — it's `position: absolute` regardless of where in the DOM it lands — but eyeball it with JS disabled/before hydration once the content is portalled, just in case.

## Verification

- `ddev npm run primitives:dev` + `ddev npm run docs:dev`; open Select and Combobox docs/Storybook examples, inspect the DOM and confirm `Content` renders just before `</body>` rather than nested inside `root`'s div.
- Exercise keyboard navigation, option selection, clear/indicator states — this is exactly what the hydration fix protects.
- Load a page with multiple instances (e.g. `Examples/All.html`) and confirm they don't cross-contaminate (should be safe — ids are namespaced by `rootId`).
- `ddev composer test:functional` — check [tests/Functional/Components/](../../packages/fluid-primitives/tests/Functional/Components/) for existing Select/Combobox rendering tests and for the portal-detection precedent in [DialogRenderingTest.php](../../packages/fluid-primitives/tests/Functional/Components/DialogRenderingTest.php); add an equivalent assertion that `Content` is portal-wrapped.
- `ddev npm run types`, `ddev npm run format:check`, `ddev composer run lint`.
