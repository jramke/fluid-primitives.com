# Switch

**A toggle component for turning a single option on or off.**

{% component: "ui:referenceButtons", arguments: { "name": "Switch" } %}

{% component: "ui:componentExample", arguments: { "componentName": "SwitchExamples.simple", "withEntryFile": true } %}

## Features

- Based on the ARIA switch pattern
- Syncs with a native checkbox for form submission
- Works with [Field](/docs/components/field)
- Supports disabled, invalid, required, and read-only states
- Full keyboard support

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

## Installation

{% component: "ui:installationSection", arguments: { "name": "Switch" } %}

## Examples

### Start checked

Set `defaultChecked` to render the switch on.

{% component: "ui:componentExample", arguments: { "componentName": "SwitchExamples.defaultChecked" } %}

### Disable the switch

Set `disabled` to lock the switch in its current state.

{% component: "ui:componentExample", arguments: { "componentName": "SwitchExamples.disabled" } %}

### Add helper text and validation

Wrap the switch in a [Field](/docs/components/field) for a helper text, validation messages and the `name` it submits under.

{% component: "ui:componentExample", arguments: { "componentName": "SwitchExamples.withField" } %}

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
            ["hiddenInput", "Provides the native checkbox input for form submission. Renders an `<input>` element, with the empty-value input that submits `''` while the switch is off in front of it."]
        ]
    }
%}
