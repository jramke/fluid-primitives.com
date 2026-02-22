# Accordion

**A set of collapsible panels with headings.**

{% component: "ui:referenceButtons", arguments: { "name": "Accordion" } %}

{% component: "ui:componentExample", arguments: { "componentName": "Accordion.examples.simple" } %}

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
