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

## Usage

```html
<ui:scrollArea.root class="h-72 w-48">
    <ui:scrollArea.viewport>
        <ui:scrollArea.content>Your scrollable content here.</ui:scrollArea.content>
    </ui:scrollArea.viewport>
    <ui:scrollArea.scrollbar>
        <ui:scrollArea.thumb />
    </ui:scrollArea.scrollbar>
</ui:scrollArea.root>
```

## Examples

### Horizontal Scroll

Enable horizontal scrolling for wide content.

```html
<ui:scrollArea.root class="w-96">
    <ui:scrollArea.viewport>
        <ui:scrollArea.content class="flex gap-4 w-max">
            <div class="w-48 h-32 bg-muted rounded">Item 1</div>
            <div class="w-48 h-32 bg-muted rounded">Item 2</div>
            <div class="w-48 h-32 bg-muted rounded">Item 3</div>
            <div class="w-48 h-32 bg-muted rounded">Item 4</div>
        </ui:scrollArea.content>
    </ui:scrollArea.viewport>
    <ui:scrollArea.scrollbar orientation="horizontal">
        <ui:scrollArea.thumb />
    </ui:scrollArea.scrollbar>
</ui:scrollArea.root>
```

### Both Directions

Support scrolling in both vertical and horizontal directions.

```html
<ui:scrollArea.root class="h-72 w-72">
    <ui:scrollArea.viewport>
        <ui:scrollArea.content class="w-[500px] h-[500px]"> Large content that scrolls in both directions. </ui:scrollArea.content>
    </ui:scrollArea.viewport>
    <ui:scrollArea.scrollbar orientation="vertical">
        <ui:scrollArea.thumb />
    </ui:scrollArea.scrollbar>
    <ui:scrollArea.scrollbar orientation="horizontal">
        <ui:scrollArea.thumb />
    </ui:scrollArea.scrollbar>
</ui:scrollArea.root>
```

### Scrollbar on Hover

Show the scrollbar only when hovering over the scroll area.

```html
<ui:scrollArea.root class="h-72 w-48" scrollbarVisibility="hover">
    <ui:scrollArea.viewport>
        <ui:scrollArea.content>
            <p>Content that scrolls...</p>
            <p>More content...</p>
            <p>Even more content...</p>
        </ui:scrollArea.content>
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
