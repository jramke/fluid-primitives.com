# Tabs

**A component for toggling between related panels on the same page.**

{% component: "ui:referenceButtons", arguments: { "name": "Tabs" } %}

{% component: "ui:tabs.examples.simple" %}

## Installation

{% component: "ui:installationSection", arguments: { "name": "Tabs" } %}

## Usage

```html
<ui:tabs.root defaultValue="tab1">
    <ui:tabs.list>
        <ui:tabs.trigger value="tab1">Tab 1</ui:tabs.trigger>
        <ui:tabs.trigger value="tab2">Tab 2</ui:tabs.trigger>
        <ui:tabs.trigger value="tab3">Tab 3</ui:tabs.trigger>
    </ui:tabs.list>
    <ui:tabs.content value="tab1">Content for tab 1.</ui:tabs.content>
    <ui:tabs.content value="tab2">Content for tab 2.</ui:tabs.content>
    <ui:tabs.content value="tab3">Content for tab 3.</ui:tabs.content>
</ui:tabs.root>
```

## Anatomy

```html
<primitives:tabs.root>
    <primitives:tabs.list>
        <primitives:tabs.trigger />
        <primitives:tabs.indicator />
    </primitives:tabs.list>
    <primitives:tabs.content />
</primitives:tabs.root>
```
