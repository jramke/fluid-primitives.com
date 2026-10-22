# Fieldset

**A group of related fields with a legend, helper text and error text, that disables everything inside it at once.**

{% component: "ui:referenceButtons", arguments: { "name": "Fieldset" } %}

{% component: "ui:componentExample", arguments: { "componentName": "FieldsetExamples.simple" } %}

## Features

- Renders a native `<fieldset>`, so browsers and screen readers announce the group with the name of its `legend`
- `disabled` reaches every control inside it and every [Field](/docs/components/field) with the primitives it wraps, including custom parts like a checkbox's control or a slider's thumb
- Helper text and error text are linked to the fieldset with `aria-describedby`, the error text shows while the fieldset is `invalid`
- A fieldset inside a disabled fieldset is disabled as well

## Anatomy

```html
<primitives:fieldset.root>
    <primitives:fieldset.legend />
    <primitives:fieldset.helperText />
    <!-- fields, checkboxes, ... -->
    <primitives:fieldset.errorText />
</primitives:fieldset.root>
```

## Installation

{% component: "ui:installationSection", arguments: { "name": "Fieldset" } %}

## Examples

### Disable a group of fields

Set `disabled` on the root and everything inside is disabled, whatever the props of the fields say. The browser leaves disabled controls out of the submit.

{% component: "ui:componentExample", arguments: { "componentName": "FieldsetExamples.disabled" } %}

### Mark a group as invalid

Set `invalid` for a rule about the group as a whole, such as "choose at least one". The fields inside keep validating themselves.

{% component: "ui:componentExample", arguments: { "componentName": "FieldsetExamples.invalid" } %}

### Disable a group at runtime

Switch a group with `updateProps({ disabled })`, the fields inside follow. Render the fieldset with `autoMount="{false}"` and mount it in your entry file to get the instance.

{% component: "ui:componentExample", arguments: { "componentName": "FieldsetExamples.toggleDisabled", "additionalFiles": {"ToggleDisabled.entry.ts": "EXT:docs/Resources/Private/Components/ui/FieldsetExamples/ToggleDisabled.entry.ts"} } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Fieldset",
        "parts": [
            ["root", "Provides the fieldset state and wraps the group. Renders a `<fieldset>` element."],
            ["legend", "Names the group. Renders a `<legend>` element, which needs to be the first child of the root."],
            ["helperText", "Explains the group. Renders a `<div>` element."],
            ["errorText", "Tells what is wrong with the group, hidden unless the fieldset is `invalid`. Renders a `<div>` element."]
        ]
    }
%}
