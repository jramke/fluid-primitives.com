# Accordion

**A set of collapsible panels with headings.**

{% component: "ui:referenceButtons", arguments: { "name": "Accordion" } %}

{% component: "ui:accordion.examples.simple" %}

## Features

- Full keyboard navigation support
- Supports single or multiple expanded panels
- Supports horizontal and vertical orientations
- Collapse each accordion item individually or together

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

## Examples

### Multiple Expanded Panels

Allow multiple accordion items to be expanded at once by setting `multiple` to true.

```html
<ui:accordion.root multiple="{true}">
    <ui:accordion.item value="item-1">
        <ui:accordion.itemTrigger>First item</ui:accordion.itemTrigger>
        <ui:accordion.itemContent>Content for the first item.</ui:accordion.itemContent>
    </ui:accordion.item>
    <ui:accordion.item value="item-2">
        <ui:accordion.itemTrigger>Second item</ui:accordion.itemTrigger>
        <ui:accordion.itemContent>Content for the second item.</ui:accordion.itemContent>
    </ui:accordion.item>
    <ui:accordion.item value="item-3">
        <ui:accordion.itemTrigger>Third item</ui:accordion.itemTrigger>
        <ui:accordion.itemContent>Content for the third item.</ui:accordion.itemContent>
    </ui:accordion.item>
</ui:accordion.root>
```

### Default Expanded Items

Use the `defaultValue` prop to set initially expanded items. Pass an array for multiple mode.

```html
<ui:accordion.root defaultValue="{0: 'item-1'}">
    <ui:accordion.item value="item-1">
        <ui:accordion.itemTrigger>Initially Open</ui:accordion.itemTrigger>
        <ui:accordion.itemContent>This item is open by default.</ui:accordion.itemContent>
    </ui:accordion.item>
    <ui:accordion.item value="item-2">
        <ui:accordion.itemTrigger>Initially Closed</ui:accordion.itemTrigger>
        <ui:accordion.itemContent>This item starts closed.</ui:accordion.itemContent>
    </ui:accordion.item>
</ui:accordion.root>
```

### Disabled Items

Disable specific accordion items to prevent interaction.

```html
<ui:accordion.root>
    <ui:accordion.item value="item-1">
        <ui:accordion.itemTrigger>Enabled item</ui:accordion.itemTrigger>
        <ui:accordion.itemContent>Content for the enabled item.</ui:accordion.itemContent>
    </ui:accordion.item>
    <ui:accordion.item value="item-2" disabled="{true}">
        <ui:accordion.itemTrigger>Disabled item</ui:accordion.itemTrigger>
        <ui:accordion.itemContent>This content cannot be accessed.</ui:accordion.itemContent>
    </ui:accordion.item>
</ui:accordion.root>
```

### Collapsible

By default, one accordion item must remain expanded. Set `collapsible` to true to allow all items to be collapsed.

```html
<ui:accordion.root collapsible="{true}">
    <ui:accordion.item value="item-1">
        <ui:accordion.itemTrigger>Collapsible item</ui:accordion.itemTrigger>
        <ui:accordion.itemContent>Click the trigger again to collapse this item.</ui:accordion.itemContent>
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
