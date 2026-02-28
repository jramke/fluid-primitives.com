# Checkbox

**An easily stylable checkbox component.**

{% component: "ui:referenceButtons", arguments: { "name": "Checkbox" } %}

{% component: "ui:componentExample", arguments: { "componentName": "Checkbox.examples.simple", "withEntryFile": true } %}

## Features

- Tri-state support (checked, unchecked, indeterminate)
- Syncs with native form elements for proper form submission
- Works with Field component for form integration
- Full keyboard support

## Installation

{% component: "ui:installationSection", arguments: { "name": "Checkbox" } %}

## Examples

### Default Checked

Set the checkbox to be checked by default.

{% component: "ui:componentExample", arguments: { "componentName": "Checkbox.examples.defaultChecked" } %}

### Indeterminate State

Use the indeterminate state for "select all" checkboxes or partial selections.

{% component: "ui:componentExample", arguments: { "componentName": "Checkbox.examples.indeterminate" } %}

### Disabled

Prevent interaction with the checkbox.

{% component: "ui:componentExample", arguments: { "componentName": "Checkbox.examples.disabled" } %}

## Anatomy

```html
<primitives:checkbox.root>
    <primitives:checkbox.control>
        <primitives:checkbox.indicator />
    </primitives:checkbox.control>
    <primitives:checkbox.label />
    <primitives:checkbox.hiddenInput />
</primitives:checkbox.root>
```
