---
preview: true
---

# Field Array

**A repeatable group of fields, for letting users add and remove rows like "add another person".**

{% component: "ui:referenceButtons", arguments: { "name": "FieldArray" } %}

{% component: "ui:componentExample", arguments: { "componentName": "FieldArrayExamples.simple", "withEntryFile": true } %}

## Features

- Add and remove rows in the browser, `name[0]`, `name[1]`, ... stay contiguous as rows are removed
- Existing rows are rendered on the server, so they show and submit without JavaScript
- Works with `Field` and the field-aware primitives (Input, Select, Checkbox, ...) in a row
- Row names use the bracket notation Extbase understands (`people[0][firstName]`)
- Optional `minItems` and `maxItems`, `addTrigger` and `removeTrigger` disable themselves at the bound
- `emptyState`, `addTrigger` and `removeTrigger` start with the right hidden or disabled state in the server-rendered HTML, derived from `itemCount`

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

## Installation

{% component: "ui:installationSection", arguments: { "name": "FieldArray" } %}

{% component: "ui:alert", arguments: {"title": "Also install Field", "text": "The fields in a row are `Field`s, so add the Field primitive too (typo3 ui:add field).", "variant": "info"} %}

## Examples

### Render the rows you already have

Write a row twice: once in `itemTemplate`, which the browser clones for every added row, and once in an `f:for` loop over your data, which the server renders. The demo at the top of the page renders two people this way.

Pass the index of the loop to each `item` as `index`. A `Field` in a row gets its name prefixed with the array name and the index, so `firstName` in row 1 of `people` becomes `people[1][firstName]`.

Pass the number of rows you render as `itemCount`, e.g. `itemCount="{people -> f:count()}"`. It lets `emptyState`, `addTrigger` and `removeTrigger` start with the right state in the server-rendered HTML, matching `minItems` and `maxItems`.

Fields in an added row hydrate once your entry file calls `mountAll` for them in `onItemAdded`.

### Start with an empty list

With `itemCount="0"`, the `emptyState` shows until the first row is added.

{% component: "ui:componentExample", arguments: { "componentName": "FieldArrayExamples.empty" } %}

### Limit the number of rows

`minItems="1"` disables `removeTrigger` once one row is left, `maxItems="3"` disables `addTrigger` at three rows. The "2 of 3 added" text is your own element, marked with `{ui:ref(name: 'status', context: 'fieldArray')}` and updated from `onItemAdded` and `onItemRemoved`.

{% component: "ui:componentExample", arguments: { "componentName": "FieldArrayExamples.limited" } %}

### Add a Select, a DatePicker and a Popover to a row

Components that render their content through `ui:portal` work in a row. An added row moves that content to the end of `<body>` (or after the `ui:portalContainer` of its name) and removes it again with the row. A `ui:portal` with `disabled` stays in the row.

The demo writes the row once in an `<f:section name="row">` that `itemTemplate` and the loop both `f:render`, and calls `mountAll` for the row's components in `onItemAdded`.

{% component: "ui:componentExample", arguments: { "componentName": "FieldArrayExamples.complex", "additionalFiles": {"Complex.entry.ts": "EXT:docs/Resources/Private/Components/ui/FieldArrayExamples/Complex.entry.ts"} } %}

### Validate a guest list

A `Form` with a `FieldArray` of guests, each with a name and an email address. Pass a [Zod schema](/docs/core-concepts/forms#content-client-side-validation) to `validation`: `z.array(z.object({...}))` covers however many rows exist, and the path of each issue (e.g. `['guests', 0, 'email']`) lands on the field of that row. The form submits once every row is valid.

{% component: "ui:componentExample", arguments: { "componentName": "GuestList", "additionalFiles": {"GuestList.entry.ts": "EXT:docs/Resources/Private/Components/GuestList/GuestList.entry.ts"} } %}

### Save rows to a persisted object

Inside a `Form` bound to a persisted object, `__trustedProperties` signs the field names of the rows rendered on the server (`guests[0][name]`, `guests[1][name]`, ...). Rows added in the browser are unsigned, and Extbase rejects unsigned names for a persisted object. Allow the properties of a row in the property mapping configuration of the argument, e.g. `forProperty('guests.*')->allowProperties('name', 'email')` in `initializeUpdateAction()`. Name them, because `allowAllProperties()` would let a visitor set every property of a row.

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "FieldArray",
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
