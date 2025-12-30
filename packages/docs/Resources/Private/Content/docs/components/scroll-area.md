# Scroll Area

**A native scroll container with custom scrollbars.**

{% component: "ui:referenceButtons", arguments: { "name": "ScrollArea" } %}

{% component: "ui:scrollArea.examples.simple" %}

## Installation

{% component: "ui:installationSection", arguments: { "name": "ScrollArea" } %}

## Usage

```html
<ui:scrollArea.root class="h-72 w-48">
    <ui:scrollArea.viewport>
        <ui:scrollArea.content> Your scrollable content here. </ui:scrollArea.content>
    </ui:scrollArea.viewport>
    <ui:scrollArea.scrollbar>
        <ui:scrollArea.thumb />
    </ui:scrollArea.scrollbar>
</ui:scrollArea.root>
```

## Anatomy

```html
<primitives:scrollArea.root>
    <primitives:scrollArea.viewport>
        <primitives:scrollArea.content />
    </primitives:scrollArea.viewport>
    <primitives:scrollArea.scrollbar>
        <primitives:scrollArea.thumb />
    </primitives:scrollArea.scrollbar>
</primitives:scrollArea.root>
```
