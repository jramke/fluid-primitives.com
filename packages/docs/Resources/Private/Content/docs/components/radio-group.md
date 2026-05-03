# Radio Group

**An easily stylable radio button component.**

{% component: "ui:referenceButtons", arguments: { "name": "RadioGroup" } %}

{% component: "ui:componentExample", arguments: { "componentName": "RadioGroup.examples.simple", "withEntryFile": true } %}

## Features

- Full keyboard navigation support
- Supports horizontal and vertical orientations
- Syncs with native form elements for proper form submission
- Works with Field component for form integration

## Installation

{% component: "ui:installationSection", arguments: { "name": "RadioGroup" } %}

## Examples

### Disabled Items

Disable specific radio options.

{% component: "ui:componentExample", arguments: { "componentName": "RadioGroup.examples.disabledItems" } %}

### Disabled Group

Disable the entire radio group.

{% component: "ui:componentExample", arguments: { "componentName": "RadioGroup.examples.disabledGroup" } %}

### No Default Value

Start with no option selected by default.

{% component: "ui:componentExample", arguments: { "componentName": "RadioGroup.examples.noDefault" } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "RadioGroup",
        "parts": [
            ["root", "Provides shared radio group state and semantics. Renders a `<div>` element."],
            ["label", "Labels the radio group. Renders a `<span>` element."],
            ["item", "Wraps a single radio item and makes it clickable. Renders a `<label>` element."],
            ["itemHiddenInput", "Provides the native radio input for form submission. Renders an `<input>` element."],
            ["itemControl", "Displays the visual radio control. Renders a `<div>` element."],
            ["itemText", "Displays the visible label text for a radio item. Renders a `<span>` element."],
            ["indicator", "Displays the moving selection indicator. Renders a `<div>` element."]
        ]
    }
%}

## Anatomy

```html
<primitives:radioGroup.root>
    <primitives:radioGroup.item>
        <primitives:radioGroup.itemControl />
        <primitives:radioGroup.itemText />
        <primitives:radioGroup.itemHiddenInput />
    </primitives:radioGroup.item>
    <primitives:radioGroup.indicator />
</primitives:radioGroup.root>
```
