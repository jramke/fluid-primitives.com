# Checkbox Group

**A group of checkboxes for selecting multiple values.**

{% component: "ui:referenceButtons", arguments: { "name": "CheckboxGroup" } %}

{% component: "ui:componentExample", arguments: { "componentName": "CheckboxGroupExamples.simple", "withEntryFile": true } %}

## Features

- Multiple selection support (unlike RadioGroup which is single selection)
- Optional maximum number of selections via `maxSelectedValues`
- Full keyboard navigation support
- Syncs with native form elements for proper form submission
- Works with Field component for form integration

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

### Default Checked

Pre-select multiple options using an array of values.

{% component: "ui:componentExample", arguments: { "componentName": "CheckboxGroupExamples.defaultChecked" } %}

### Disabled Items

Disable specific checkbox options.

{% component: "ui:componentExample", arguments: { "componentName": "CheckboxGroupExamples.disabledItems" } %}

### Disabled Group

Disable the entire checkbox group.

{% component: "ui:componentExample", arguments: { "componentName": "CheckboxGroupExamples.disabledGroup" } %}

### Maximum Selections

Limit the number of selectable options. Once the limit is reached, remaining unchecked options are automatically disabled.

{% component: "ui:componentExample", arguments: { "componentName": "CheckboxGroupExamples.maxSelected" } %}

### Select All

Implement a "Select All" checkbox that toggles all options.

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
