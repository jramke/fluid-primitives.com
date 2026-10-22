# Checkbox Group

**A group of checkboxes for selecting multiple values.**

{% component: "ui:referenceButtons", arguments: { "name": "CheckboxGroup" } %}

{% component: "ui:componentExample", arguments: { "componentName": "CheckboxGroupExamples.simple", "withEntryFile": true } %}

## Features

- Selects several values at once
- Optional limit on the selection with `maxSelectedValues`
- Full keyboard navigation support
- Syncs with native inputs for form submission
- Works with the Field component

## Anatomy

```html
<primitives:checkboxGroup.root>
    <primitives:checkboxGroup.label />
    <primitives:checkbox.root>
        <primitives:checkbox.control>
            <primitives:checkbox.indicator />
        </primitives:checkbox.control>
        <primitives:checkbox.label />
        <primitives:checkbox.hiddenInput />
    </primitives:checkbox.root>
</primitives:checkboxGroup.root>
```

## Installation

{% component: "ui:installationSection", arguments: { "name": "CheckboxGroup" } %}

## Examples

### Default checked

Pass the values to preselect as a list in `defaultValue`. The notification preferences start with email and push checked.

{% component: "ui:componentExample", arguments: { "componentName": "CheckboxGroupExamples.defaultChecked" } %}

### Disabled items

Set `disabled` on a single `checkbox.root` to lock that option while the others stay interactive.

{% component: "ui:componentExample", arguments: { "componentName": "CheckboxGroupExamples.disabledItems" } %}

### Disabled group

Set `disabled` on `checkboxGroup.root` to lock every option at once.

{% component: "ui:componentExample", arguments: { "componentName": "CheckboxGroupExamples.disabledGroup" } %}

### Maximum selections

Set `maxSelectedValues` to cap the selection. Pick two colors in the demo and the remaining one is disabled until you uncheck another.

{% component: "ui:componentExample", arguments: { "componentName": "CheckboxGroupExamples.maxSelected" } %}

### Select all

A checkbox above the group checks or clears every option, and shows the indeterminate state while only some are checked. The entry file tab shows how the two are wired.

{% component: "ui:componentExample", arguments: { "componentName": "CheckboxGroupExamples.selectAll", "additionalFiles": {"SelectAll.entry.ts": "EXT:docs/Resources/Private/Components/ui/CheckboxGroupExamples/SelectAll.entry.ts"} } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "CheckboxGroup",
        "parts": [
            ["root", "Provides shared state for a group of related checkboxes. Renders a `<div>` element."],
            ["label", "Labels the checkbox group. Renders a `<span>` element."]
        ]
    }
%}
