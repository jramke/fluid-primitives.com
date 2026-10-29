# Checkbox

**A checkbox with a checked, unchecked and indeterminate state.**

{% component: "ui:referenceButtons", arguments: { "name": "Checkbox" } %}

{% component: "ui:componentExample", arguments: { "componentName": "CheckboxExamples.simple", "withEntryFile": true } %}

## Features

- Tri-state support (checked, unchecked, indeterminate)
- Syncs with a native input for form submission
- Works with the Field component
- Full keyboard support

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

## Installation

{% component: "ui:installationSection", arguments: { "name": "Checkbox" } %}

## Examples

### Default checked

Pass `defaultChecked="{true}"` to render the checkbox checked.

{% component: "ui:componentExample", arguments: { "componentName": "CheckboxExamples.defaultChecked" } %}

### Indeterminate state

Pass `defaultChecked="indeterminate"` for a "select all" checkbox while only some of its options are selected.

{% component: "ui:componentExample", arguments: { "componentName": "CheckboxExamples.indeterminate" } %}

### Disabled

Pass `disabled="{true}"` to block interaction with the checkbox.

{% component: "ui:componentExample", arguments: { "componentName": "CheckboxExamples.disabled" } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Checkbox",
        "parts": [
            ["root", "Groups the checkbox control, label, and input into one interactive label. Renders a `<label>` element."],
            ["hiddenInput", "Provides the native checkbox input for form submission and browser integration. Renders an `<input>` element, with the empty-value input that submits `''` while the box is unchecked in front of it."],
            ["control", "Displays the visual checkbox box that reflects the checked state. Renders a `<div>` element."],
            ["indicator", "Displays the checked or indeterminate indicator inside the control. Renders a `<div>` element."],
            ["label", "Displays the visible label text for the checkbox. Renders a `<span>` element."]
        ]
    }
%}
