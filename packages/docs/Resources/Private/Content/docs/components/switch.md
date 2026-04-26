# Switch

**A toggle component for turning a single option on or off.**

{% component: "ui:referenceButtons", arguments: { "name": "Switch" } %}

{% component: "ui:componentExample", arguments: { "componentName": "Switch.examples.simple", "withEntryFile": true } %}

## Features

- Based on the ARIA switch pattern
- Syncs with native form elements for proper form submission
- Works with Field component for form integration
- Supports disabled, invalid, required, and read-only states
- Full keyboard support

## Installation

{% component: "ui:installationSection", arguments: { "name": "Switch" } %}

## Examples

### Default Checked

Set the switch to be checked by default.

{% component: "ui:componentExample", arguments: { "componentName": "Switch.examples.defaultChecked" } %}

### Disabled

Prevent interaction with the switch.

{% component: "ui:componentExample", arguments: { "componentName": "Switch.examples.disabled" } %}

### With Form Field

Use the switch with the Field component for descriptions and validation.

{% component: "ui:componentExample", arguments: { "componentName": "Switch.examples.withField" } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Switch",
        "parts": [
            ["root", "Groups the switch control, label, and input into one interactive label. Renders a `<label>` element."],
            ["label", "Displays the visible label text for the switch. Renders a `<span>` element."],
            ["control", "Displays the visual switch track. Renders a `<span>` element."],
            ["thumb", "Displays the movable thumb inside the switch track. Renders a `<span>` element."],
            ["indicator", "Displays UI for a specific switch state. Renders a `<span>` element."],
            ["hiddenInput", "Provides the native checkbox input for form submission. Renders an `<input>` element."]
        ]
    }
%}

## Anatomy

```html
<primitives:switch.root>
    <primitives:switch.control>
        <primitives:switch.thumb />
    </primitives:switch.control>
    <primitives:switch.label />
    <primitives:switch.hiddenInput />
</primitives:switch.root>
```
