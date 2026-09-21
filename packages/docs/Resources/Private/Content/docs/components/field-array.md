# Field Array

**A repeatable group of fields, for letting users add and remove rows like "add another person".**

{% component: "ui:referenceButtons", arguments: { "name": "FieldArray", "skipZag": true } %}

{% component: "ui:componentExample", arguments: { "componentName": "FieldArray.examples.simple", "withEntryFile": true } %}

## Features

- Add and remove rows client-side, with contiguous `name[0]`, `name[1]`, ... indexing maintained automatically as rows are removed
- Existing rows render for real on the server - no JavaScript required to see or submit data that's already there
- Works with `Field` and any Field-aware primitive (Input, Select, Checkbox, ...) nested inside a row
- Row `name`s use the same bracket notation (`people[0][firstName]`) Extbase and `FormValues` already understand
- Optional `minItems`/`maxItems` bounds - `addTrigger`/`removeTrigger` disable themselves once a bound is reached, and `append()`/`remove()` are no-ops past it
- `emptyState`/`addTrigger`/`removeTrigger` already reflect the correct hidden/disabled state in the server-rendered HTML, from `itemCount`, not only once JavaScript hydrates

## Installation

{% component: "ui:installationSection", arguments: { "name": "FieldArray" } %}

{% component: "ui:alert", arguments: {"title": "Also install Field", "text": "FieldArray only manages rows and indexing - each row's own fields still need the Field primitive (typo3 ui:add field) nested inside.", "variant": "info"} %}

## How It Works

A row's markup is authored once, inside `itemTemplate` - a `<template>` element that's never rendered directly, only cloned client-side whenever `addTrigger` is clicked. Existing rows are a separate, ordinary loop over data you already have, rendered for real:

```html
<primitives:fieldArray.itemTemplate>
    <primitives:fieldArray.item>
        <!-- one row's fields, authored once -->
    </primitives:fieldArray.item>
</primitives:fieldArray.itemTemplate>

<primitives:fieldArray.itemGroup>
    <f:for each="{people}" as="person" iteration="it">
        <primitives:fieldArray.item index="{it.index}">
            <!-- the same row markup, authored again with this person's data -->
        </primitives:fieldArray.item>
    </f:for>
</primitives:fieldArray.itemGroup>
```

Yes, the row is written twice - once for the stencil, once for the loop. This isn't a shortcut taken for `FieldArray` specifically; it's the same pattern `FileUpload` already uses for mixing already-uploaded files with newly-picked ones (see its docs' "Editing" section), and it's what keeps every row genuinely server-rendered rather than reconstructed from JSON after the page loads.

Each `fieldArray.item`'s `index` prop (omitted inside `itemTemplate`, where no real index exists yet) automatically prefixes every nested `Field`'s `name` - `<ui:field name="firstName">` inside row `1` of an array named `people` becomes `people[1][firstName]` without you writing that out yourself.

### Reflecting Row Count on the Server

`root` takes a required `itemCount` prop - the number of rows you're about to render in the loop above. Nothing counts your rows for you (they're your own `f:for` loop, not a collection `FieldArray` owns), so pass it explicitly, e.g. `itemCount="{people -> f:count()}"`. It's what lets `emptyState`/`addTrigger`/`removeTrigger` start in the correct hidden/disabled state in the server-rendered HTML itself, matching `minItems`/`maxItems`, rather than only correcting themselves once JavaScript hydrates.

```html
<primitives:fieldArray.root name="people" itemCount="{people -> f:count()}">
    <!-- ... -->
</primitives:fieldArray.root>
```

## Examples

### Starting Empty

A `FieldArray` with no rows yet - `emptyState` shows until the first row is added.

{% component: "ui:componentExample", arguments: { "componentName": "FieldArray.examples.empty" } %}

### Limiting Row Count

`minItems="1"` disables `removeTrigger` once a single row remains; `maxItems="3"` disables `addTrigger` once three rows exist. The status text below the rows ("2 of 3 added") isn't a `FieldArray` feature by itself - it's a plain element the row markup authors itself (`{ui:ref(name: 'status', context: 'fieldArray')}`), kept in sync from `onItemAdded`/`onItemRemoved` the same way this example already mounts each row's own `Field`/`Input`.

{% component: "ui:componentExample", arguments: { "componentName": "FieldArray.examples.limited" } %}

### Full Form With Client-Side Validation

A complete `Form` wrapping a `FieldArray` of guests, each with a `name`/`email` pair - required fields and the email format are validated live as you type or blur, using a [Zod schema](/docs/core-concepts/forms#client-side-validation) passed straight to `validation`. `z.array(z.object({...}))` covers however many guest rows currently exist (added or removed) without `FieldArray` needing to know about validation at all - each issue's own path (e.g. `['guests', 0, 'email']`) is matched back to the exact row's own field automatically. Submission is blocked until every row is valid.

{% component: "ui:componentExample", arguments: { "componentName": "GuestList", "additionalFiles": {"GuestList.entry.ts": "EXT:docs/Resources/Private/Components/GuestList/GuestList.entry.ts"} } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "FieldArray",
        "skipZag": true,
        "parts": [
            ["root", "Contains every part of the field array. Renders a `<div>` element."],
            ["itemTemplate", "Wraps one row's markup, authored once and cloned client-side for each added row. Renders a `<template>` element - never visible itself."],
            ["itemGroup", "Groups every row, existing and added. Renders a `<div>` element."],
            ["item", "One row. Existing rows are authored directly with an `index`; added rows are cloned from `itemTemplate`, which omits it. Renders a `<div>` element."],
            ["emptyState", "Shown while `itemGroup` has no rows yet. Renders a `<div>` element."],
            ["addTrigger", "Appends a new row, cloned from `itemTemplate`. Renders a `<button>` element."],
            ["removeTrigger", "Removes its enclosing row and re-indexes every later row down by one. Renders a `<button>` element."]
        ]
    }
%}

## Anatomy

```html
<primitives:fieldArray.root itemCount="{items -> f:count()}">
    <primitives:fieldArray.itemTemplate>
        <primitives:fieldArray.item>
            <!-- Your row's fields here -->
            <primitives:fieldArray.removeTrigger />
        </primitives:fieldArray.item>
    </primitives:fieldArray.itemTemplate>
    <primitives:fieldArray.itemGroup>
        <f:for each="{items}" as="item">
            <primitives:fieldArray.item index="{...}">
                <!-- Your row's fields here -->
                <primitives:fieldArray.removeTrigger />
            </primitives:fieldArray.item>
        </f:for>
        <primitives:fieldArray.emptyState />
    </primitives:fieldArray.itemGroup>
    <primitives:fieldArray.addTrigger />
</primitives:fieldArray.root>
```
