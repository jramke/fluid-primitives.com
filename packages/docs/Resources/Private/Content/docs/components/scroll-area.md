# Scroll Area

**A native scroll container with custom scrollbars.**

{% component: "ui:referenceButtons", arguments: { "name": "ScrollArea" } %}

{% component: "ui:componentExample", arguments: { "componentName": "ScrollAreaExamples.simple", "withEntryFile": true } %}

## Features

- Native browser scrolling
- Supports vertical and horizontal scrolling
- Customizable scrollbar appearance
- Scrollbar visible always, while scrolling or on hover
- Scrollbar thumb follows the scroll position

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

## Installation

{% component: "ui:installationSection", arguments: { "name": "ScrollArea" } %}

## Examples

### Scroll horizontally

Add a horizontal `scrollbar` for wide content, like a row of product cards.

{% component: "ui:componentExample", arguments: { "componentName": "ScrollAreaExamples.horizontal" } %}

### Scroll in both directions

Add one `scrollbar` per orientation for content that is wider and taller than the area, like a large table.

{% component: "ui:componentExample", arguments: { "componentName": "ScrollAreaExamples.bothDirections" } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "ScrollArea",
        "parts": [
            ["root", "Provides the scroll area container and shared state. Renders a `<div>` element."],
            ["viewport", "Provides the native scrollable viewport. Renders a `<div>` element."],
            ["content", "Wraps the scrollable content to measure its size. Renders a `<div>` element."],
            ["scrollbar", "Displays a custom scrollbar track. Renders a `<div>` element."],
            ["thumb", "Displays the draggable scrollbar thumb. Renders a `<div>` element."]
        ]
    }
%}
