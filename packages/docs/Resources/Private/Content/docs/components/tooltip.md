# Tooltip

**A popup that appears when an element is hovered or focused, showing a hint for sighted users.**

{% component: "ui:referenceButtons", arguments: { "name": "Tooltip" } %}

{% component: "ui:componentExample", arguments: { "componentName": "Tooltip.examples.simple" } %}

## Features

- Opens on hover and focus
- Closes on pointer down or Escape key press
- Supports custom open and close delays
- Supports custom positioning with placement options
- Automatically adjusts position to stay in viewport

## Installation

{% component: "ui:installationSection", arguments: { "name": "Tooltip" } %}

## Examples

### Custom Positioning

Control where the tooltip appears relative to the trigger.

{% component: "ui:componentExample", arguments: { "componentName": "Tooltip.examples.customPositioning" } %}

### Open Delay

Set a custom delay before the tooltip opens.

{% component: "ui:componentExample", arguments: { "componentName": "Tooltip.examples.openDelay" } %}

### Close Delay

Set a custom delay before the tooltip closes after the pointer leaves.

{% component: "ui:componentExample", arguments: { "componentName": "Tooltip.examples.closeDelay" } %}

### Disabled Interaction

Prevent the tooltip from opening on hover.

{% component: "ui:componentExample", arguments: { "componentName": "Tooltip.examples.disabled" } %}

### On Icon Buttons

Common pattern for icon-only buttons that need accessible labels.

{% component: "ui:componentExample", arguments: { "componentName": "Tooltip.examples.iconButton" } %}

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
