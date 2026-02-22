# Clipboard

**A component to copy text to the clipboard.**

{% component: "ui:referenceButtons", arguments: { "name": "Clipboard" } %}

{% component: "ui:componentExample", arguments: { "componentName": "Clipboard.examples.simple" } %}

## Features

- Supports copying text to the clipboard
- Visual feedback when copying is successful
- Supports custom timeout for the copied state

## Installation

{% component: "ui:installationSection", arguments: { "name": "Clipboard" } %}

## Examples

### Copy Button Only

Display only a copy button without the input field.

{% component: "ui:componentExample", arguments: { "componentName": "Clipboard.examples.copyButtonOnly" } %}

### Custom Timeout

Set a custom duration for how long the "copied" state is shown.

{% component: "ui:componentExample", arguments: { "componentName": "Clipboard.examples.customTimeout" } %}

## Anatomy

```html
<primitives:clipboard.root>
    <primitives:clipboard.label />
    <primitives:clipboard.control>
        <primitives:clipboard.input />
        <primitives:clipboard.trigger>
            <primitives:clipboard.indicator />
        </primitives:clipboard.trigger>
    </primitives:clipboard.control>
</primitives:clipboard.root>
```
