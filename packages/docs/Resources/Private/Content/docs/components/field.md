# Field

**A form field wrapper that provides accessible labeling, error handling, and state management for form inputs.**

{% component: "ui:referenceButtons", arguments: { "name": "Field", "skipZag": true } %}

{% component: "ui:componentExample", arguments: { "componentName": "Field.examples.simple", "withEntryFile": true } %}

## Features

- Automatic label association with form controls
- Error message display with proper ARIA attributes
- Description text support for additional context
- State management for disabled, required, readonly, and invalid states
- Seamless integration with Form component for validation
- Works with all form-related primitives (Checkbox, Select, RadioGroup, etc.) and native inputs

## Installation

{% component: "ui:installationSection", arguments: { "name": "Field" } %}

## Examples

### Required Field

Mark a field as required.

{% component: "ui:componentExample", arguments: { "componentName": "Field.examples.required" } %}

### With Description

Add helpful description text below the input.

{% component: "ui:componentExample", arguments: { "componentName": "Field.examples.withDescription" } %}

### Invalid State

Indicate that the field has an error and display an error message.

{% component: "ui:componentExample", arguments: { "componentName": "Field.examples.invalid" } %}

### Disabled Field

Disable the entire field.

{% component: "ui:componentExample", arguments: { "componentName": "Field.examples.disabled" } %}

### With Checkbox

Use with the Checkbox component.

{% component: "ui:componentExample", arguments: { "componentName": "Field.examples.withCheckbox" } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Field",
        "skipZag": true,
        "parts": [
            ["root", "Provides shared field state for labels, descriptions, errors, and controls. Renders a `<div>` element."],
            ["label", "Labels the associated form control. Renders a `<label>` element."],
            ["control", "Wraps the slotted form control and wires up shared field attributes. Renders the element defined by the `asChild` prop."],
            ["description", "Displays help or supporting text for the field. Renders a `<div>` element."],
            ["error", "Displays validation error messages for the field. Renders a `<div>` element."]
        ]
    }
%}

## Anatomy

```html
<primitives:field.root>
    <primitives:field.label />
    <primitives:field.control asChild="{true}">
        <!-- Your form input here -->
    </primitives:field.control>
    <primitives:field.description />
    <primitives:field.error />
</primitives:field.root>
```
