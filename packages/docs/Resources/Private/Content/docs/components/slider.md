# Slider

**An input for selecting a value, or a range of values, from a given range.**

{% component: "ui:referenceButtons", arguments: { "name": "Slider" } %}

{% component: "ui:componentExample", arguments: { "componentName": "Slider.examples.simple", "withEntryFile": true } %}

## Features

- Full keyboard navigation support with arrow, page and home/end keys
- Supports touch, mouse and pointer interactions, including clicking the track
- Supports a range of values with multiple thumbs, with a configurable minimum gap between them
- Supports custom step, `largeStep` and min/max values
- Supports marks/ticks along the track
- Accepts `defaultValue` as a single number for one thumb, or a list of numbers for a range
- Works with the Field component for form integration

## Installation

{% component: "ui:installationSection", arguments: { "name": "Slider" } %}

## Examples

### Range

Use two thumbs to let users pick a range of values.

{% component: "ui:componentExample", arguments: { "componentName": "Slider.examples.range" } %}

### With marks

Render `slider.marker` elements to show ticks - with a label - along the track.

{% component: "ui:componentExample", arguments: { "componentName": "Slider.examples.withMarks" } %}

### Disabled

Prevent the slider from being interacted with.

{% component: "ui:componentExample", arguments: { "componentName": "Slider.examples.disabled" } %}

### With a number input

Pair the slider with a `number-input` for precise entry, keeping both in sync by updating one
whenever the other changes. Both are mounted as independent, hydration-controlled instances
(`controlled="{true}"` + a fixed `rootId`) so a custom entry file can wire them together, the same
pattern [Combobox's custom filter example](/docs/components/combobox#custom-filter-api) uses. Both
components' own `onValueChange` call into one shared `setValue()` function instead of writing to
each other directly, so there's a single guard - skip if it's already the current value - rather
than a `syncing` flag guessing which call is the "real" one.

{% component: "ui:componentExample", arguments: { "componentName": "Slider.examples.withNumberInput", "additionalFiles": {"WithNumberInput.entry.ts": "EXT:docs/Resources/Private/Components/ui/Slider/Examples/WithNumberInput.entry.ts"} } %}

### With a Field

Wrap the slider in [`ui:field.root`](/docs/components/field) to get label association and
`name`/`disabled`/`invalid` state propagated to the thumb automatically - a donation amount picker,
with marks for common preset amounts.

{% component: "ui:componentExample", arguments: { "componentName": "Slider.examples.withField" } %}

### With a dragging indicator

Nest `slider.draggingIndicator` inside a `slider.thumb` to show its current value in a small
tooltip while it's being dragged - it's hidden the rest of the time.

{% component: "ui:componentExample", arguments: { "componentName": "Slider.examples.withDraggingIndicator" } %}

### With decimal values

Set `step` to a fraction (e.g. `0.01`) to get fine-grained, decimal precision instead of whole
numbers - useful whenever the value represents something more precise than an integer count.

{% component: "ui:componentExample", arguments: { "componentName": "Slider.examples.withDecimalValues" } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Slider",
        "parts": [
            ["root", "Contains every part of the slider. Renders a `<div>` element."],
            ["label", "Renders an accessible label for the slider. Renders a `<label>` element."],
            ["valueText", "Displays a textual representation of the current value. Renders a `<span>` element."],
            ["control", "Wraps the track and thumbs, and handles pointer interaction with the track. Renders a `<div>` element."],
            ["track", "Renders the full length of the slider's track. Renders a `<div>` element."],
            ["range", "Renders the filled portion of the track between the origin and the thumb(s). Renders a `<div>` element."],
            ["thumb", "Renders a draggable handle for one value. Takes an `index` prop to identify which value it controls. Renders a `<div>` element."],
            ["hiddenInput", "Provides a native input for form submission, nested inside a `slider.thumb`. Renders an `<input>` element."],
            ["markerGroup", "Groups the marks/ticks rendered along the track. Renders a `<div>` element."],
            ["marker", "Renders a single mark/tick at a given value. Renders a `<div>` element."],
            ["draggingIndicator", "Displays the current value while its thumb is being dragged. Nested inside a `slider.thumb`, whose index it follows automatically. Renders a `<div>` element."]
        ]
    }
%}

## Anatomy

```html
<primitives:slider.root>
    <primitives:slider.label />
    <primitives:slider.valueText />
    <primitives:slider.control>
        <primitives:slider.track>
            <primitives:slider.range />
        </primitives:slider.track>
        <primitives:slider.thumb index="0">
            <primitives:slider.hiddenInput />
        </primitives:slider.thumb>
        <primitives:slider.markerGroup>
            <primitives:slider.marker value="25" />
        </primitives:slider.markerGroup>
    </primitives:slider.control>
</primitives:slider.root>
```
