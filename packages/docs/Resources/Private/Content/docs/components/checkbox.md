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

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Checkbox",
        "parts": [
            ["root", "Groups the checkbox control, label, and input into one interactive label. Renders a `<label>` element."],
            ["hiddenInput", "Provides the native checkbox input for form submission and browser integration. Renders an `<input>` element."],
            ["control", "Displays the visual checkbox box that reflects the checked state. Renders a `<div>` element."],
            ["indicator", "Displays the checked or indeterminate indicator inside the control. Renders a `<div>` element."],
            ["label", "Displays the visible label text for the checkbox. Renders a `<span>` element."]
        ]
    }
%}

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
