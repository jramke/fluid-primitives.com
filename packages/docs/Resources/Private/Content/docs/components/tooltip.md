# Tooltip

**A popup that appears when an element is hovered or focused, showing a hint for sighted users.**

{% component: "ui:referenceButtons", arguments: { "name": "Tooltip" } %}

{% component: "ui:componentExample", arguments: { "componentName": "TooltipExamples.simple", "withEntryFile": true } %}

## Features

- Opens on hover and focus
- Closes on pointer down or Escape key press
- Supports custom open and close delays
- Supports custom positioning with placement options
- Automatically adjusts position to stay in viewport

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

## Installation

{% component: "ui:installationSection", arguments: { "name": "Tooltip" } %}

## Examples

### Change the placement

Pass `placement` through the `positioning` prop. The tooltip below opens to the right of its trigger.

{% component: "ui:componentExample", arguments: { "componentName": "TooltipExamples.customPositioning" } %}

### Delay the opening

Set `openDelay` in milliseconds, the default is 400. The tooltip below appears one second after you hover the button.

{% component: "ui:componentExample", arguments: { "componentName": "TooltipExamples.openDelay" } %}

### Delay the closing

Set `closeDelay` in milliseconds, the default is 150. The tooltip below stays visible for 500 ms after the pointer leaves.

{% component: "ui:componentExample", arguments: { "componentName": "TooltipExamples.closeDelay" } %}

### Disable the tooltip

Set `disabled` to keep the tooltip closed, for example while the action behind a button is unavailable.

{% component: "ui:componentExample", arguments: { "componentName": "TooltipExamples.disabled" } %}

### Label an icon button

Give an icon-only button a tooltip for sighted visitors and a visually hidden label for screen readers.

{% component: "ui:componentExample", arguments: { "componentName": "TooltipExamples.iconButton" } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Tooltip",
        "parts": [
            ["root", "Provides tooltip state and context for the composed parts. Renders no wrapper element."],
            ["trigger", "Acts as the anchor that opens the tooltip. Renders a `<button>` element."],
            ["positioner", "Positions the tooltip content relative to the trigger. Renders a `<div>` element."],
            ["content", "Displays the tooltip bubble content. Renders a `<div>` element."],
            ["arrow", "Displays a decorative tooltip arrow. Renders a `<div>` element."]
        ]
    }
%}
