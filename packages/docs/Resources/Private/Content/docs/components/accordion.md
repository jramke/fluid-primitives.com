# Accordion

**A set of collapsible panels with headings.**

{% component: "ui:referenceButtons", arguments: { "name": "Accordion" } %}

{% component: "ui:componentExample", arguments: { "componentName": "Accordion.examples.simple", "withEntryFile": true } %}

## Features

- Full keyboard navigation support
- Supports single or multiple expanded panels
- Supports horizontal and vertical orientations

## Installation

{% component: "ui:installationSection", arguments: { "name": "Accordion" } %}

## Examples

### Multiple Expanded Panels

Allow multiple accordion items to be expanded at once by setting `multiple` to true.

{% component: "ui:componentExample", arguments: { "componentName": "Accordion.examples.multiple" } %}

### Disabled Items

Disable specific accordion items to prevent interaction.

{% component: "ui:componentExample", arguments: { "componentName": "Accordion.examples.disabled" } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Accordion",
        "parts": [
            ["root", "Groups all parts of the accordion. Renders a `<div>` element."],
            ["item", "Groups an accordion header with the corresponding content. Renders a `<div>` element."],
            ["itemHeader", "A heading that labels the corresponding content. Renders an `<h3>` element."],
            ["itemContent", "The content of the accordion item. Renders a `<div>` element."],
            ["itemTrigger", "A button that opens and closes the corresponding item. Renders a `<button>` element."],
            ["itemIndicator", "An optional visual indicator that can be used to show the open/closed state of the item. Renders a `<div>` element."]
        ]
    }
%}

## Anatomy

```html
<primitives:accordion.root>
    <primitives:accordion.item>
        <primitives:accordion.itemHeader>
            <primitives:accordion.itemTrigger>
                <primitives:accordion.itemIndicator />
            </primitives:accordion.itemTrigger>
        </primitives:accordion.itemHeader>
        <primitives:accordion.itemContent />
    </primitives:accordion.item>
</primitives:accordion.root>
```
