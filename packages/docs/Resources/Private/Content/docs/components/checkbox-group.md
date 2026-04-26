# Checkbox Group

**A group of checkboxes for selecting multiple values.**

{% component: "ui:referenceButtons", arguments: { "name": "CheckboxGroup", "skipZag": true } %}

{% component: "ui:componentExample", arguments: { "componentName": "CheckboxGroup.examples.simple", "withEntryFile": true } %}

## Features

- Multiple selection support (unlike RadioGroup which is single selection)
- Optional maximum number of selections via `maxSelectedValues`
- Full keyboard navigation support
- Syncs with native form elements for proper form submission
- Works with Field component for form integration

## Installation

{% component: "ui:installationSection", arguments: { "name": "CheckboxGroup" } %}

## Examples

### Default Checked

Pre-select multiple options using an array of values.

{% component: "ui:componentExample", arguments: { "componentName": "CheckboxGroup.examples.defaultChecked" } %}

### Disabled Items

Disable specific checkbox options.

{% component: "ui:componentExample", arguments: { "componentName": "CheckboxGroup.examples.disabledItems" } %}

### Disabled Group

Disable the entire checkbox group.

{% component: "ui:componentExample", arguments: { "componentName": "CheckboxGroup.examples.disabledGroup" } %}

### Maximum Selections

Limit the number of selectable options. Once the limit is reached, remaining unchecked options are automatically disabled.

{% component: "ui:componentExample", arguments: { "componentName": "CheckboxGroup.examples.maxSelected" } %}

### Select All

Implement a "Select All" checkbox that toggles all options.

{% component: "ui:componentExample", arguments: { "componentName": "CheckboxGroup.examples.selectAll" } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "CheckboxGroup",
        "skipZag": true,
        "parts": [
            ["root", "Provides shared state for a group of related checkboxes. Renders a `<div>` element."],
            ["label", "Labels the checkbox group. Renders a `<span>` element."]
        ]
    }
%}

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
