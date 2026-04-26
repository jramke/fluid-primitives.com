# Scroll Area

**A native scroll container with custom scrollbars.**

{% component: "ui:referenceButtons", arguments: { "name": "ScrollArea" } %}

{% component: "ui:componentExample", arguments: { "componentName": "ScrollArea.examples.simple", "withEntryFile": true } %}

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
