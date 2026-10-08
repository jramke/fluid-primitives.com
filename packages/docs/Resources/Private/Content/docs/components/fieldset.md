# Fieldset

**A group of related fields with a legend, helper text and error text, that disables everything inside it at once.**

{% component: "ui:referenceButtons", arguments: { "name": "Fieldset" } %}

{% component: "ui:componentExample", arguments: { "componentName": "FieldsetExamples.simple" } %}

## Features

- Renders a native `<fieldset>`, so browsers and screen readers announce the group with the name of its `legend`
- `disabled` reaches every control inside it, and every [Field](/docs/components/field) with the primitives it wraps - also the parts that are no native control, like a checkbox's control or a slider's thumb
- Helper text and error text are linked to the fieldset with `aria-describedby`, the error text is only shown while the fieldset is `invalid`
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

### Disabled

Set `disabled` on the root. The fieldset carries it to everything inside, whatever the props of the fields say, and the browser skips them when the form is submitted.

{% component: "ui:componentExample", arguments: { "componentName": "FieldsetExamples.disabled" } %}

### Invalid

`invalid` marks the group as a whole, e.g. "choose at least one". It is not passed on to the fields inside, they keep validating themselves.

{% component: "ui:componentExample", arguments: { "componentName": "FieldsetExamples.invalid" } %}

### Toggling Disabled at Runtime

Fields watch the `disabled` attribute of the fieldset they are in, so changing it afterwards reaches them too. Mount the fieldset yourself (`autoMount="{false}"`) to get hold of the instance, and update its props:

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
