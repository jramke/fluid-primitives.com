# Number Input

**A numeric input with increment and decrement buttons.**

{% component: "ui:referenceButtons", arguments: { "name": "NumberInput" } %}

{% component: "ui:componentExample", arguments: { "componentName": "NumberInputExamples.simple", "withEntryFile": true } %}

## Features

- Follows the spinbutton pattern
- Changes the value with the mouse wheel through `allowMouseWheel`
- Avoids floating point rounding errors when incrementing, decrementing and snapping to a step
- Keeps stepping while a button is pressed and held
- Rounds the value to a set number of fraction digits
- Scrubbing: change the value by dragging
- Takes its locale from the site language

## Anatomy

```html
<primitives:numberInput.root>
    <primitives:numberInput.label />
    <primitives:numberInput.control>
        <primitives:numberInput.decrementTrigger />
        <primitives:numberInput.input />
        <primitives:numberInput.incrementTrigger />
    </primitives:numberInput.control>
    <primitives:numberInput.scrubber />
    <primitives:numberInput.valueText />
</primitives:numberInput.root>
```

## Installation

{% component: "ui:installationSection", arguments: { "name": "NumberInput" } %}

## Examples

### With min/max constraints

Pass `min` and `max` to set the range and `step` to set the increment. The buttons stop at the limits, and a typed value snaps into the range when the input loses focus. The demo is a percentage from 0 to 100 in steps of 5.

{% component: "ui:componentExample", arguments: { "componentName": "NumberInputExamples.minMax" } %}

### With format options

Pass `formatOptions`, an object of [Intl.NumberFormat options](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Intl/NumberFormat), to round the value to a number of decimals or show it as a currency. The client formats the value, so the server renders it unformatted until the number input hydrates.

{% component: "ui:componentExample", arguments: { "componentName": "NumberInputExamples.formatOptions" } %}

### With scrubber

Wrap an element in `numberInput.scrubber` to change the value by dragging it horizontally. In the demo that element is the label.

{% component: "ui:componentExample", arguments: { "componentName": "NumberInputExamples.withScrubber" } %}

### Mouse wheel support

Set `allowMouseWheel="{true}"` to change the value with the mouse wheel while the input has focus. Focus the input in the demo and scroll.

{% component: "ui:componentExample", arguments: { "componentName": "NumberInputExamples.mouseWheel" } %}

### Localization

The labels of the increment and decrement buttons ship in the language file and follow the site language. Pass `translations` to override them for one number input, for example for a quantity stepper in a shop.

```html
<f:variable
    name="numberInputTranslations"
    value="{
        incrementLabel: '{f:translate(key: \'LLL:EXT:my_extension/Resources/Private/Language/locallang.xlf:forms.quantity.increase\')}',
        decrementLabel: '{f:translate(key: \'LLL:EXT:my_extension/Resources/Private/Language/locallang.xlf:forms.quantity.decrease\')}'
    }"
/>

<ui:numberInput.root translations="{numberInputTranslations}"> ... </ui:numberInput.root>
```

`valueText` sets the `aria-valuetext` of the input, the text a screen reader reads for the value. Write it with a `%value%` placeholder, e.g. `valueText: '%value% pieces'`. A client script passes a function of the value instead.

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "NumberInput",
        "parts": [
            ["root", "Provides shared number input state and wraps all related parts. Renders a `<div>` element."],
            ["label", "Labels the number input. Renders a `<label>` element."],
            ["control", "Groups the input and stepper triggers. Renders a `<div>` element."],
            ["input", "Provides the editable spinbutton input. Renders an `<input>` element."],
            ["incrementTrigger", "Increases the current value. Renders a `<button>` element."],
            ["decrementTrigger", "Decreases the current value. Renders a `<button>` element."],
            ["scrubber", "Allows changing the value by dragging. Renders a `<div>` element."],
            ["valueText", "Displays the formatted value as text. Renders a `<span>` element."]
        ]
    }
%}
