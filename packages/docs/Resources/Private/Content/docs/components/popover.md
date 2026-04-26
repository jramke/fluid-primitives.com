# Popover

**An accessible popup anchored to a button.**

{% component: "ui:referenceButtons", arguments: { "name": "Popover" } %}

{% component: "ui:componentExample", arguments: { "componentName": "Popover.examples.simple", "withEntryFile": true } %}

## Features

- Focus is managed and can be trapped within the popover
- Supports modal and non-modal modes
- Supports custom positioning with placement options
- Pressing `Escape` closes the popover
- Automatically adjusts position to stay in viewport

## Installation

{% component: "ui:installationSection", arguments: { "name": "Popover" } %}

## Examples

### With Close Button

Show a close button inside the popover.

{% component: "ui:componentExample", arguments: { "componentName": "Popover.examples.withCloseButton" } %}

### Custom Positioning

Control where the popover appears relative to the trigger.

{% component: "ui:componentExample", arguments: { "componentName": "Popover.examples.customPositioning" } %}

### Modal Mode

Make the popover modal to trap focus and block interaction with the rest of the page.

{% component: "ui:componentExample", arguments: { "componentName": "Popover.examples.modal" } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Popover",
        "parts": [
            ["root", "Provides popover state and context for the composed parts. Renders no wrapper element."],
            ["trigger", "Opens and closes the popover. Renders a `<button>` element."],
            ["positioner", "Positions the popover content relative to the trigger. Renders a `<div>` element."],
            ["content", "Contains the popover panel content. Renders a `<div>` element."],
            ["arrow", "Displays a decorative arrow for the popover. Renders a `<div>` element."],
            ["indicator", "Displays a decorative state indicator. Renders a `<div>` element."],
            ["title", "Provides the accessible title for the popover. Renders a `<div>` element."],
            ["description", "Provides supporting descriptive text for the popover. Renders a `<div>` element."],
            ["closeTrigger", "Closes the popover when activated. Renders a `<button>` element."]
        ]
    }
%}

## Anatomy

```html
<primitives:popover.root>
    <primitives:popover.trigger />
    <primitives:popover.positioner>
        <primitives:popover.content>
            <primitives:popover.arrow />
            <primitives:popover.closeTrigger />
            <primitives:popover.title />
            <primitives:popover.description />
        </primitives:popover.content>
    </primitives:popover.positioner>
</primitives:popover.root>
```
