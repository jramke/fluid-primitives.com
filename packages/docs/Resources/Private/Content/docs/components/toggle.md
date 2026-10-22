# Toggle

**A button that toggles between pressed and unpressed.**

{% component: "ui:referenceButtons", arguments: { "name": "Toggle" } %}

{% component: "ui:componentExample", arguments: { "componentName": "ToggleExamples.simple", "withEntryFile": true } %}

## Features

- Native `<button>` with `aria-pressed`
- Toggles with <kbd>Space</kbd> and <kbd>Enter</kbd>
- Can start pressed or disabled
- Optional indicator that follows the pressed state
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

### Start pressed

Set `defaultPressed` to render the toggle pressed.

{% component: "ui:componentExample", arguments: { "componentName": "ToggleExamples.defaultPressed" } %}

### Disable a toggle

Set `disabled` to lock a toggle in its current state, pressed or unpressed.

{% component: "ui:componentExample", arguments: { "componentName": "ToggleExamples.disabled" } %}

### Show the state with an indicator

The indicator carries the same `data-pressed` and `data-disabled` attributes as the root. The dot below changes color while the toggle is pressed.

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
