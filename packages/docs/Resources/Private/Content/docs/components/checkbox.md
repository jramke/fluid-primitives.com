# Checkbox

**An easily stylable checkbox component.**

{% component: "ui:referenceButtons", arguments: { "name": "Checkbox" } %}

{% component: "ui:componentExample", arguments: { "componentName": "Checkbox.examples.simple" } %}

## Features

- Tri-state support (checked, unchecked, indeterminate)
- Syncs with native form elements for proper form submission
- Works with Field component for form integration
- Full keyboard support

## Installation

{% component: "ui:installationSection", arguments: { "name": "Checkbox" } %}

## Usage

```html
<ui:checkbox.root>
    <ui:checkbox.control />
    <ui:checkbox.label>Accept terms and conditions</ui:checkbox.label>
</ui:checkbox.root>
```

## Examples

### Default Checked

Set the checkbox to be checked by default.

```html
<ui:checkbox.root defaultChecked="{true}">
    <ui:checkbox.control />
    <ui:checkbox.label>Checked by default</ui:checkbox.label>
</ui:checkbox.root>
```

### Indeterminate State

Use the indeterminate state for "select all" checkboxes or partial selections.

```html
<ui:checkbox.root defaultChecked="indeterminate">
    <ui:checkbox.control />
    <ui:checkbox.label>Select all (some selected)</ui:checkbox.label>
</ui:checkbox.root>
```

### Disabled

Prevent interaction with the checkbox.

```html
<ui:checkbox.root disabled="{true}">
    <ui:checkbox.control />
    <ui:checkbox.label>Disabled checkbox</ui:checkbox.label>
</ui:checkbox.root>
```

### With Form Field

Use with the Field component for form validation and error handling.

```html
<ui:field.root name="terms" required="{true}">
    <ui:checkbox.root>
        <ui:checkbox.control />
        <ui:field.label>
            <ui:checkbox.label>I accept the terms and conditions</ui:checkbox.label>
        </ui:field.label>
    </ui:checkbox.root>
    <ui:field.error />
</ui:field.root>
```

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
