# Checkbox

**An easily stylable checkbox component.**

{% component: "ui:referenceButtons", arguments: { "name": "Checkbox" } %}

{% component: "ui:checkbox.examples.simple" %}

## Installation

{% component: "ui:installationSection", arguments: { "name": "Checkbox" } %}

## Usage

```html
<ui:checkbox.root>
    <ui:checkbox.control />
    <ui:checkbox.label>Accept terms and conditions</ui:checkbox.label>
</ui:checkbox.root>
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
