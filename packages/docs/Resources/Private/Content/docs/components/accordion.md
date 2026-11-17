# Accordion

**A set of collapsible panels with headings.**

{% component: "ui:referenceButtons", arguments: { "name": "Accordion" } %}

{% component: "ui:componentExample", arguments: { "componentName": "AccordionExamples.simple", "withEntryFile": true } %}

## Features

- Keyboard accessible: <kbd>Tab</kbd> moves between the triggers, <kbd>Enter</kbd> and <kbd>Space</kbd> toggle a panel
- Supports single or multiple expanded panels
- Supports horizontal and vertical orientations
- Opens a panel the browser's search finds
- Animates the content height like [Collapsible](/docs/components/collapsible)

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

## Installation

{% component: "ui:installationSection", arguments: { "name": "Accordion" } %}

## Examples

### Allow several open panels

Set `multiple` to keep several panels open at once. Try opening two items below.

{% component: "ui:componentExample", arguments: { "componentName": "AccordionExamples.multiple" } %}

### Disable single items

Set `disabled` on an `accordion.item` to keep its panel closed. The second item below is disabled.

{% component: "ui:componentExample", arguments: { "componentName": "AccordionExamples.disabled" } %}

### Find closed panels with the browser's search

Set `hiddenUntilFound` on the root and the browser's find in page (<kbd>Ctrl</kbd> or <kbd>Cmd</kbd> + <kbd>F</kbd>) searches closed panels too. A match opens its item. Search for "returns" or "business days" below.

{% component: "ui:componentExample", arguments: { "componentName": "AccordionExamples.findInPage" } %}

A style that resets `[hidden]` hides these panels from the search. Leave `until-found` out of the reset:

```css
[hidden]:not([hidden='until-found']) {
    display: none;
}
```

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Accordion",
        "parts": [
            ["root", "Groups all parts of the accordion. Renders a `<div>` element."],
            ["item", "Groups an accordion header with the corresponding content. Renders a `<div>` element."],
            ["itemHeader", "A heading that labels the corresponding content. Renders an `<h3>` element."],
            ["itemContent", "The content of the accordion item. Renders a `<div>` element."],
            ["itemTrigger", "A button that opens and closes the corresponding item. Renders a `<button>` element."],
            ["itemIndicator", "An optional visual indicator that can be used to show the open/closed state of the item. Renders a `<div>` element."]
        ]
    }
%}
