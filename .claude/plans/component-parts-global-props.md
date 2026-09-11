# Show global props per part in the docs, fix asChild's over-eager injection

**Status:** Not started

**Note:** breaking changes are fine — this library is pre-v0/v1 with no external compatibility contract yet. Don't add back-compat shims, deprecated fallbacks, or feature flags to soften the fix below; just make the clean change.

## Goal

1. Show `class`, `asChild`, and `attributes` (the arbitrary-HTML-attributes bag from `ui:attributes()`) in each part's **Arguments** table in the docs, instead of hiding them everywhere.
2. Stop injecting `asChild` into parts that render no element of their own, where it's currently a silent no-op — investigated and resolved below (keep the prop mechanism; just stop advertising/attaching it where it can't do anything). Detect this **explicitly** (does the part register itself as an element), not via a proxy like `class` usage.

## How the tables are built today

- Markdown pages call a live Fluid component, not a static generator, e.g. [dialog.md:71-86](../../packages/docs/Resources/Private/Content/docs/components/dialog.md#L71):
    ```
    {% component: "ui:ComponentPropsTable", arguments: { "name": "Dialog", "parts": [["root", "..."], ["trigger", "..."], ...] } %}
    ```
- [ComponentPropsTableContext::getPartsWithProps()](../../packages/docs/Classes/Components/Contexts/ComponentPropsTableContext.php#L18) resolves each `[part, description]` pair to a `ComponentDefinition` via [AbstractComponentCollection::getComponentDefinition()](../../packages/fluid-primitives/Classes/Component/AbstractComponentCollection.php#L71), which parses the part's actual `.html` template for its `<ui:prop>` declarations, then synthesizes framework-wide args on top (lines 125-200): `asChild` (always), `rootId`/`ids`/`controlled` (root parts only), `class` (only if the template source matches `{class}` usage), `attributes` (only if `ui:attributes(` is used), `spreadProps` (always, if not already set).
- Today, `getPartsWithProps()` filters **all** of `Constants::GLOBAL_PROPS` (`ids, attributes, asChild, rootId, controlled, spreadProps, class`) out of every table, so none of them ever appear, regardless of relevance.

## Investigation: can `asChild` be omitted from elementless parts?

Confirmed via `grep` across every `Resources/Private/Primitives/*/*.html` file containing `<f:slot />`: only four files render `<f:slot />` **without** rendering a wrapping element of their own:

- [Dialog/Root.html](../../packages/fluid-primitives/Resources/Private/Primitives/Dialog/Root.html) — declares behavioral props, then just `<f:slot />`.
- [Popover/Root.html](../../packages/fluid-primitives/Resources/Private/Primitives/Popover/Root.html) — same shape.
- [Tooltip/Root.html](../../packages/fluid-primitives/Resources/Private/Primitives/Tooltip/Root.html) — same shape.
- [FileUpload/ItemTemplate.html](../../packages/fluid-primitives/Resources/Private/Primitives/FileUpload/ItemTemplate.html) — unrelated (a `<template>` for client-side cloning), out of scope here.

`asChild` is currently injected unconditionally into _every_ part's argument definitions (`AbstractComponentCollection.php:125-131`), before any conditional check runs. But `asChild`'s actual mechanism — [ComponentRenderer::spreadComponentAttributesToChild()](../../packages/fluid-primitives/Classes/Component/ComponentRenderer.php#L531) — regex-extracts an opening tag + attributes from the component's _own rendered HTML_ and merges them onto the slotted child's first tag (triggered at [ComponentRenderer.php:360-365](../../packages/fluid-primitives/Classes/Component/ComponentRenderer.php#L360)). When a part's rendered HTML is nothing but its slot content (no wrapper tag), there is nothing to extract — `asChild="{true}"` on `dialog.root`/`popover.root`/`tooltip.root` is accepted but does nothing.

## Detection signal: "does this part register an element", not "does it happen to use `{class}`"

An earlier pass at this considered reusing the `class`-detection regex already in the method as the "renders an element" signal, but that regex actually tracks stylability, not element-hood, and the codebase already has a counter-example: [Combobox/Input.html](../../packages/fluid-primitives/Resources/Private/Primitives/Combobox/Input.html) renders a real, hydratable `<input>` (`{ui:ref(name: 'input', ...)}`, `{ui:attributes()}`) but has no `{class}` support at all — under the class-based signal it would wrongly be treated as "renders no element" and lose `asChild` (and it already gets no `class` prop today, which is a separate, pre-existing gap worth a one-line drive-by fix but is not this plan's job to fully audit).

Instead, key off `ui:ref(` usage in the raw template source — the same `str_contains()` style already used one block down for the `ui:attributes(` → `attributes` prop, so this isn't new machinery, just the same pattern applied to the marker that actually means "this part exposes itself as a hydratable DOM element": every part's own [ui:ref ViewHelper](../../packages/fluid-primitives/Classes/ViewHelpers/RefViewHelper.php) call is what stamps `id`/`data-scope`/`data-part` onto that part's root tag for hydration, so its presence is a direct, purpose-built signal rather than a proxy.

Verified by grep across every `Primitives/*/*.html` file: **`ui:ref(` is present in every file except exactly the same four listed above** (Dialog/Popover/Tooltip `Root` + `FileUpload/ItemTemplate`) — a perfect match, and it also correctly includes `Combobox/Input.html`. Cross-checked [Field/Control.html](../../packages/fluid-primitives/Resources/Private/Primitives/Field/Control.html), which _requires_ `asChild` (`<ui:error when="!{asChild}" .../>`): it calls `{ui:ref(name: 'control')}`, so it correctly keeps `asChild` under this rule too.

## Decision & implementation

Keep the `asChild` argument/mechanism itself (so `Field.Control`-style "this part must render as something the caller chooses" still works), but only synthesize it into a part's argument definitions when its template contains a `ui:ref(` call.

Concretely, in [AbstractComponentCollection::getComponentDefinition()](../../packages/fluid-primitives/Classes/Component/AbstractComponentCollection.php#L125): move `$templateString = $this->getTemplatePaths()->getTemplateSource(...)` (currently computed later, at line 159) up to right after the `RESERVED_PROPS` validation loop, then gate the existing unconditional `asChild` block behind `if (str_contains($templateString, 'ui:ref('))`. Leave the `class`-detection regex, root-only args (`rootId`/`ids`/`controlled`), and `spreadProps` untouched — this is a breaking change to `asChild`'s availability on a handful of parts, which is fine per the note at the top of this doc, but keep it scoped to just `asChild`.

## Second change: stop blanket-hiding global props in the docs table

In [ComponentPropsTableContext::getPartsWithProps()](../../packages/docs/Classes/Components/Contexts/ComponentPropsTableContext.php#L29-L33), replace the `!in_array($value, Constants::GLOBAL_PROPS, true)` filter with a smaller, locally-defined exclusion list containing only the internal/plumbing props that should stay hidden regardless of part: `ids`, `rootId`, `controlled`, `spreadProps`. **Do not** change `Constants::GLOBAL_PROPS` itself — it's also consumed by [ext_localconf.php:30](../../packages/fluid-primitives/ext_localconf.php#L30) to exclude these from Storybook's auto-generated controls, and that usage should be unaffected.

Once this filter shrinks, `class`, `asChild`, **and `attributes`** will show up in a part's table exactly when they're present in that part's (now-corrected) argument definitions — i.e. automatically hidden for Dialog/Popover/Tooltip's `root` rows, and shown everywhere else. `attributes` is confirmed in scope: surface it as a documented "arbitrary attributes bag" for any part using `ui:attributes()`, the same way `class`/`asChild` are surfaced.

## Drive-by fix while in this area

[combobox.md:110](../../packages/docs/Resources/Private/Content/docs/components/combobox.md#L110)'s (currently commented-out) API Reference block describes `root` as _"Provides dialog state and context for the composed parts. Renders no wrapper element."_ — copy-pasted from `dialog.md` and wrong on both counts (says "dialog", and Combobox's `Root` does render a `<div>`, per [Combobox/Root.html](../../packages/fluid-primitives/Resources/Private/Primitives/Combobox/Root.html)). Fix the text to mirror [select.md:76](../../packages/docs/Resources/Private/Content/docs/components/select.md#L76)'s equivalent line before that block is ever uncommented.

## Docs prose to update for consistency

[core-concepts/composition.md](../../packages/docs/Resources/Private/Content/docs/core-concepts/composition.md) ("The `asChild` Prop" section) and/or [core-concepts/arguments.md](../../packages/docs/Resources/Private/Content/docs/core-concepts/arguments.md) ("Automatic Props" section) currently describe `asChild`/`class` as something "every component receives automatically" with no caveat. Add a short note that a part's own Arguments table is now the source of truth for whether it actually accepts them — e.g. root parts that render no wrapper element (Dialog, Popover, Tooltip) don't.

## Verification

- `ddev npm run docs:dev`; check Dialog/Popover/Tooltip's `root` rows no longer show `asChild` (and still don't show `class`), Select/Combobox's `root` row and every Trigger/Content/Item-type row across components now show `class`/`asChild`/`attributes` wherever each applies, Field's `control` row still shows `asChild` (must not regress — it's required there), and Combobox's `input` row now shows `asChild`/`attributes` (confirming the `ui:ref`-based signal catches it even though it still correctly shows no `class` row).
- `ddev composer test:functional`; search `tests/` for anything asserting on `GLOBAL_PROPS`, `asChild`, or `ComponentPropsTableContext` output and update expectations.
- `ddev composer run lint` / `ddev npm run format:check`.
