# Toggle

**A button that toggles between pressed and unpressed.**

{% component: "ui:referenceButtons", arguments: { "name": "Toggle" } %}

{% component: "ui:componentExample", arguments: { "componentName": "ToggleExamples.simple", "withEntryFile": true } %}

## Features

- Renders a native `<button>` with `aria-pressed`, toggled with <kbd>Space</kbd> and <kbd>Enter</kbd>
- Can be pressed by default and disabled
- Optional indicator part that follows the pressed state
- For a set of related toggles, see [Toggle Group](/docs/components/toggle-group)

## Anatomy

```html
<primitives:toggle.root>
    <primitives:toggle.indicator />
</primitives:toggle.root>
```

## Installation

{% component: "ui:installationSection", arguments: { "name": "Toggle" } %}

## Examples

### Default Pressed

Set the toggle to be pressed by default.

{% component: "ui:componentExample", arguments: { "componentName": "ToggleExamples.defaultPressed" } %}

### Disabled

Prevent the toggle from being pressed.

{% component: "ui:componentExample", arguments: { "componentName": "ToggleExamples.disabled" } %}

### Indicator

The indicator carries the same `data-pressed` and `data-disabled` attributes as the root, so it can show the state of the toggle without any extra markup.

{% component: "ui:componentExample", arguments: { "componentName": "ToggleExamples.indicator" } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Toggle",
        "parts": [
            ["root", "Provides the pressed state and toggles it on click. Renders a `<button>` element."],
            ["indicator", "Visual indicator of the pressed state, e.g. an icon or a dot. Renders a `<span>` element."]
        ]
    }
%}
