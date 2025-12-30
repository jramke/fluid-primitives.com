# Select

**A common form component for choosing a predefined value in a dropdown menu.**

{% component: "ui:referenceButtons", arguments: { "name": "Select" } %}

{% component: "ui:select.examples.simple" %}

## Installation

{% component: "ui:installationSection", arguments: { "name": "Select" } %}

## Usage

```html
<ui:listCollection
    items="{
        0: {value: 'apple', label: 'Apple'},
        1: {value: 'banana', label: 'Banana'},
        2: {value: 'cherry', label: 'Cherry'}
    }"
    as="collection"
/>

<ui:select.root collection="{collection}">
    <ui:select.label>Select a fruit</ui:select.label>
    <ui:select.control>
        <ui:select.trigger placeholder="Choose a fruit" />
    </ui:select.control>
    <ui:select.content>
        <f:for each="{collection.items}" as="item">
            <ui:select.item item="{item}">
                <ui:select.itemText>{item.label}</ui:select.itemText>
                <ui:select.itemIndicator />
            </ui:select.item>
        </f:for>
    </ui:select.content>
</ui:select.root>
```

## Anatomy

```html
<primitives:select.root>
    <primitives:select.label />
    <primitives:select.control>
        <primitives:select.trigger>
            <primitives:select.valueText />
            <primitives:select.indicator />
        </primitives:select.trigger>
        <primitives:select.clearTrigger />
    </primitives:select.control>
    <primitives:select.positioner>
        <primitives:select.content>
            <primitives:select.item>
                <primitives:select.itemText />
                <primitives:select.itemIndicator />
            </primitives:select.item>
            <primitives:select.itemGroup>
                <primitives:select.itemGroupLabel />
            </primitives:select.itemGroup>
        </primitives:select.content>
    </primitives:select.positioner>
    <primitives:select.hiddenSelect />
</primitives:select.root>
```
