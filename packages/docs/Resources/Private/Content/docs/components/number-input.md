# Number Input

**A numeric input component with increment and decrement controls.**

{% component: "ui:referenceButtons", arguments: { "name": "NumberInput" } %}

{% component: "ui:componentExample", arguments: { "componentName": "NumberInput.examples.simple" } %}

## Features

- Based on the spinbutton pattern
- Supports using the scroll wheel to increment and decrement the value
- Handles floating point rounding errors when incrementing, decrementing, and snapping to step
- Supports pressing and holding the spin buttons to continuously increment or decrement
- Supports rounding value to specific number of fraction digits
- Support for scrubbing interaction
- Automatically sets the locale based on Site Language

## Installation

{% component: "ui:installationSection", arguments: { "name": "NumberInput" } %}

## Usage

```html
<ui:numberInput.root>
    <ui:numberInput.label>Quantity</ui:numberInput.label>
    <ui:numberInput.control>
        <ui:numberInput.decrementTrigger />
        <ui:numberInput.input />
        <ui:numberInput.incrementTrigger />
    </ui:numberInput.control>
</ui:numberInput.root>
```

## Examples

### With Min/Max Constraints

Pass the `min` prop or `max` prop to set an upper and lower limit for the input. By default, the input will restrict the value to stay within the specified range.

```html
<ui:numberInput.root min="0" max="100" step="5">
    <ui:numberInput.label>Percentage</ui:numberInput.label>
    <ui:numberInput.control>
        <ui:numberInput.decrementTrigger />
        <ui:numberInput.input />
        <ui:numberInput.incrementTrigger />
    </ui:numberInput.control>
</ui:numberInput.root>
```

### With Default Value

```html
<ui:numberInput.root defaultValue="50">
    <ui:numberInput.label>Initial Value</ui:numberInput.label>
    <ui:numberInput.control>
        <ui:numberInput.decrementTrigger />
        <ui:numberInput.input />
        <ui:numberInput.incrementTrigger />
    </ui:numberInput.control>
</ui:numberInput.root>
```

### With Format Options

You can format the input value to be rounded to specific decimal points or to be displayed as a currency by passing an object in shape of [Intl.NumberFormatOptions](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Intl/NumberFormat). Note that this is a client-only API so the initial value rendered on the server will not be formatted.

```html
<ui:numberInput.root formatOptions="{style: 'currency', currency: 'USD'}">
    <ui:numberInput.label>Price</ui:numberInput.label>
    <ui:numberInput.control>
        <ui:numberInput.decrementTrigger />
        <ui:numberInput.input />
        <ui:numberInput.incrementTrigger />
    </ui:numberInput.control>
</ui:numberInput.root>
```

### With Scrubber

The scrubber allows users to change the value by clicking and dragging horizontally.

```html
<ui:numberInput.root>
    <ui:numberInput.scrubber>
        <ui:numberInput.label>Drag to adjust</ui:numberInput.label>
    </ui:numberInput.scrubber>
    <ui:numberInput.control>
        <ui:numberInput.decrementTrigger />
        <ui:numberInput.input />
        <ui:numberInput.incrementTrigger />
    </ui:numberInput.control>
</ui:numberInput.root>
```

### Mouse Wheel Support

Enable changing the value with the mouse wheel when the input is focused.

```html
<ui:numberInput.root allowMouseWheel="{true}">
    <ui:numberInput.label>Scroll to change</ui:numberInput.label>
    <ui:numberInput.control>
        <ui:numberInput.decrementTrigger />
        <ui:numberInput.input />
        <ui:numberInput.incrementTrigger />
    </ui:numberInput.control>
</ui:numberInput.root>
```

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
