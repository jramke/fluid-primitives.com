# Slider

**A slider for picking a value, or a range of values, between a minimum and a maximum.**

{% component: "ui:referenceButtons", arguments: { "name": "Slider" } %}

{% component: "ui:componentExample", arguments: { "componentName": "SliderExamples.simple", "withEntryFile": true } %}

## Features

- Full keyboard navigation support with arrow, page and home/end keys
- Touch, mouse and pointer input, including clicks on the track
- A range of values with several thumbs and a minimum gap between them
- Custom `step`, `largeStep`, `min` and `max`
- Marks along the track
- `defaultValue` as a single number for one thumb, or a list of numbers for a range
- Works with the Field component

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

## Installation

{% component: "ui:installationSection", arguments: { "name": "Slider" } %}

## Examples

### Range

Render a `slider.thumb` for each end of the range, with `index` `0` and `1`, and pass two numbers as `defaultValue`. `minStepsBetweenThumbs="10"` keeps the thumbs of the price range at least 10 steps apart.

{% component: "ui:componentExample", arguments: { "componentName": "SliderExamples.range" } %}

### With marks

Add `slider.marker` elements inside a `slider.markerGroup` to show labeled ticks along the track.

{% component: "ui:componentExample", arguments: { "componentName": "SliderExamples.withMarks" } %}

### Disabled

Set `disabled="{true}"` to block interaction with the slider.

{% component: "ui:componentExample", arguments: { "componentName": "SliderExamples.disabled" } %}

### With a number input

Pair the slider with a [number input](/docs/components/number-input) for precise entry: dragging the thumb updates the number, and typing a number moves the thumb. Both are mounted by hand (`autoMount="{false}"` with a fixed `rootId`, see [Mount one instance yourself](/docs/client-utilities/mounting#content-mount-one-instance-yourself)) so the entry file can connect them.

{% component: "ui:componentExample", arguments: { "componentName": "SliderExamples.withNumberInput", "additionalFiles": {"WithNumberInput.entry.ts": "EXT:docs/Resources/Private/Components/ui/SliderExamples/WithNumberInput.entry.ts"} } %}

### With a field

Wrap the slider in [`ui:field.root`](/docs/components/field) to pass `name`, `disabled` and `invalid` to the thumb. The demo is a donation amount picker with marks at common amounts.

{% component: "ui:componentExample", arguments: { "componentName": "SliderExamples.withField" } %}

### With a dragging indicator

Nest `slider.draggingIndicator` inside a `slider.thumb` to show the current value in a small tooltip while that thumb is dragged.

{% component: "ui:componentExample", arguments: { "componentName": "SliderExamples.withDraggingIndicator" } %}

### With decimal values

Set `step` to a fraction like `0.01` for decimal precision, for example for prices or ratings.

{% component: "ui:componentExample", arguments: { "componentName": "SliderExamples.withDecimalValues" } %}

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
