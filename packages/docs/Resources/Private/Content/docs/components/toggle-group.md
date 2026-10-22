# Toggle Group

**A set of toggle buttons where one or several can be pressed.**

{% component: "ui:referenceButtons", arguments: { "name": "ToggleGroup" } %}

{% component: "ui:componentExample", arguments: { "componentName": "ToggleGroupExamples.simple", "withEntryFile": true } %}

## Features

- Single or multiple selection
- Roving focus with arrow keys, <kbd>Home</kbd> and <kbd>End</kbd>
- Supports horizontal and vertical orientations
- Can keep one item pressed
- Supports disabled items and a disabled group

## Anatomy

```html
<primitives:toggleGroup.root>
    <primitives:toggleGroup.item />
</primitives:toggleGroup.root>
```

## Installation

{% component: "ui:installationSection", arguments: { "name": "ToggleGroup" } %}

## Examples

### Allow several pressed items

Set `multiple` so items press independently. Bold, italic and underline below combine.

{% component: "ui:componentExample", arguments: { "componentName": "ToggleGroupExamples.multiple" } %}

### Stack the items vertically

Set `orientation` to vertical. The up and down arrow keys move the focus.

{% component: "ui:componentExample", arguments: { "componentName": "ToggleGroupExamples.vertical" } %}

### Disable items

Set `disabled` on the group to disable all its items, or on single items. The first group below is disabled, in the second the Center item is.

{% component: "ui:componentExample", arguments: { "componentName": "ToggleGroupExamples.disabled" } %}

### Keep one item pressed

Set `deselectable="{false}"` for a choice like a billing period, where one option stays selected. `multiple` ignores it.

{% component: "ui:componentExample", arguments: { "componentName": "ToggleGroupExamples.notDeselectable" } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "ToggleGroup",
        "parts": [
            ["root", "Provides the shared state and wraps the items. Renders a `<div>` element with `role=\"group\"`."],
            ["item", "A toggle button of the group. Renders a `<button>` element."]
        ]
    }
%}
