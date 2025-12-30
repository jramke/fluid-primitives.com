# Tooltip

**A popup that appears when an element is hovered or focused, showing a hint for sighted users.**

{% component: "ui:referenceButtons", arguments: { "name": "Tooltip" } %}

{% component: "ui:tooltip.examples.simple" %}

## Installation

{% component: "ui:installationSection", arguments: { "name": "Tooltip" } %}

## Usage

```html
<ui:tooltip.root>
    <ui:tooltip.trigger asChild="{true}">
        <ui:button>Hover me</ui:button>
    </ui:tooltip.trigger>
    <ui:tooltip.content> This is the tooltip content. </ui:tooltip.content>
</ui:tooltip.root>
```

## Anatomy

```html
<primitives:tooltip.root>
    <primitives:tooltip.trigger />
    <primitives:tooltip.positioner>
        <primitives:tooltip.content>
            <primitives:tooltip.arrow />
        </primitives:tooltip.content>
    </primitives:tooltip.positioner>
</primitives:tooltip.root>
```
