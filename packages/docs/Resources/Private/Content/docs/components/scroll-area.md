# Scroll Area

**A native scroll container with custom scrollbars.**

{% component: "ui:referenceButtons", arguments: { "name": "ScrollArea" } %}

{% component: "ui:componentExample", arguments: { "componentName": "ScrollArea.examples.simple" } %}

## Features

- Uses native browser scrolling for performance
- Supports vertical and horizontal scrolling
- Customizable scrollbar appearance
- Supports visibility modes: always, scroll, or hover
- Scrollbar thumb reflects the actual scroll position

## Installation

{% component: "ui:installationSection", arguments: { "name": "ScrollArea" } %}

## Examples

### Horizontal Scroll

Enable horizontal scrolling for wide content.

{% component: "ui:componentExample", arguments: { "componentName": "ScrollArea.examples.horizontal" } %}

### Both Directions

Support scrolling in both vertical and horizontal directions.

{% component: "ui:componentExample", arguments: { "componentName": "ScrollArea.examples.bothDirections" } %}

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
