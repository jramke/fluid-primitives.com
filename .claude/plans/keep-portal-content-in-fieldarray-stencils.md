# Keep portaled content inside FieldArray row stencils

## Problem

A component that portals part of its markup (Popover, Select, Combobox, Tooltip, DatePicker, ...) loses
that markup in a FieldArray row that is added on the client. The row is cloned from the
`itemTemplate` stencil, and the portaled parts (positioner, content, ...) are not in it: they were
rendered into the page's portal container, once, with the stencil's placeholder root id.

Found while checking the date picker inside a FieldArray row (2026-10-08): the cloned row's picker
mounted and its trigger toggled `aria-expanded`, but there was no calendar. The `<template>` held the
`root`, `label`, `control`, `input` and `trigger` parts only, and
`[data-date-picker-content="<stencil root id>"]` sat in the page footer.

Server-rendered rows (real items in `fieldArray.itemGroup`) are not stencils and work.

## Reproduction

Verified with a throwaway functional test (not kept), rendering:

```html
<primitives:fieldArray.root name="dates" itemCount="0">
    <primitives:fieldArray.itemTemplate>
        <primitives:fieldArray.item>
            <primitives:popover.root>
                <primitives:popover.trigger>Open</primitives:popover.trigger>
                <ui:portal>
                    <primitives:popover.positioner>
                        <primitives:popover.content>Stencil popover</primitives:popover.content>
                    </primitives:popover.positioner>
                </ui:portal>
            </primitives:popover.root>
        </primitives:fieldArray.item>
    </primitives:fieldArray.itemTemplate>
</primitives:fieldArray.root>
```

The rendered HTML does not contain "Stencil popover", while
`PortalRegistry::getInstance()->getAllByName('default')` does.

## Cause

`TemplateViewHelper::render()` sets `isRenderStencil` on the context of the component that owns the
`ui:template` (here the FieldArray) while it renders its children.
`PortalViewHelper::isRenderingInsideAStencil()` reads the `context` variable of the template the
portal sits in, which is the nested component's own context (`PopoverContext`, `DatePickerContext`,
...). That context never carries the flag, so the portal does not render in place.

The docblock of `PortalViewHelper` names this very case ("a Popover nested in a FieldArray row's
itemTemplate"), but the existing test `rendersInAUiTemplateStencilInsteadOfPortaling` only covers a
portal that sits directly in the template of the stencil's owner.

## Proposed fix

`NestedComponentRegistry` already knows whether a stencil is being rendered:
`TemplateViewHelper` wraps the stencil's children in `pushTrackingScope($stencilKey)` and
`popTrackingScope()`, and every root component that renders in between is recorded as nested in it.
Let the portal render in place while such a scope is active (add a small accessor to the registry),
instead of relying on a flag on one component's context. Rows that are not stencils keep portaling.

## Verification

1. A functional test in `tests/Functional/ViewHelpers/PortalViewHelperTest.php`: a nested component
   with a portal inside a `ui:template` / `fieldArray.itemTemplate` keeps its portaled markup in the
   template and leaves `PortalRegistry` empty. A control with the same markup in a real item still
   portals. Run it with
   `ddev exec "cd packages/fluid-primitives && php ../../vendor/bin/phpunit -c phpunit.xml --filter PortalViewHelperTest"`
   (`ddev composer test -- --filter` does not pass the filter on), then the full functional suite.
2. In the browser on the docs site: build the library first (`ddev npm run primitives:build`, the docs
   load `dist`), then add a row to a FieldArray whose `itemTemplate` holds a Popover, Select or date
   picker. `docs/Resources/Private/Components/ui/FieldArray/FieldArray.entry.ts` only mounts Field and
   Input in `onItemAdded`, so a test entry has to mount the nested component too (`mountAll`).
   Check that the clone has the portaled parts and that opening it, picking something and
   dismissing it work.

## Not verified yet

After the fix, look at what happens to a nested component when its row is renamed (removing an
earlier row reindexes the later ones): `ComponentHydrator.renameValue()` rewrites the part
attributes in the DOM and `hydrator.rootId`, but a Zag machine keeps the `id` it was created with, so
its next render may write the old root id back onto the parts it spreads, and `hydrator.query()`
would stop finding them. Field and Input are own machines that may not be affected. Check this with a
server-rendered row that holds a Popover or Select before relying on renamed rows.
