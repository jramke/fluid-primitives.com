# Toggle Group

**A set of toggle buttons where one or several can be pressed.**

{% component: "ui:referenceButtons", arguments: { "name": "ToggleGroup" } %}

{% component: "ui:componentExample", arguments: { "componentName": "ToggleGroupExamples.simple", "withEntryFile": true } %}

## Features

- Single or multiple selection
- Roving focus: the group is one tab stop and the arrow keys move between the items, with <kbd>Home</kbd> and <kbd>End</kbd> to jump to the first and last item
- Supports horizontal and vertical orientations
- Optionally prevents the pressed item from being deselected
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

### Multiple

Allow several items to be pressed at the same time.

{% component: "ui:componentExample", arguments: { "componentName": "ToggleGroupExamples.multiple" } %}

### Vertical Orientation

Stack the items vertically. The arrow keys follow the orientation.

{% component: "ui:componentExample", arguments: { "componentName": "ToggleGroupExamples.vertical" } %}

### Disabled

Disable the whole group, or single items. A disabled group always wins over an item, an item can not opt out of it.

{% component: "ui:componentExample", arguments: { "componentName": "ToggleGroupExamples.disabled" } %}

### Not Deselectable

Keep one item always pressed. This has no effect with `multiple`.

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
