# Accordion

**A set of collapsible panels with headings.**

{% component: "ui:referenceButtons", arguments: { "name": "Accordion" } %}

{% component: "ui:accordion.examples.simple" %}

## Installation

{% component: "ui:installationSection", arguments: { "name": "Accordion" } %}

## Usage

```html
<ui:accordion.root>
    <ui:accordion.item value="item-1">
        <ui:accordion.itemTrigger>First item</ui:accordion.itemTrigger>
        <ui:accordion.itemContent>Content for the first item.</ui:accordion.itemContent>
    </ui:accordion.item>
    <ui:accordion.item value="item-2">
        <ui:accordion.itemTrigger>Second item</ui:accordion.itemTrigger>
        <ui:accordion.itemContent>Content for the second item.</ui:accordion.itemContent>
    </ui:accordion.item>
</ui:accordion.root>
```

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
