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

## Usage

```html
<ui:clipboard.root value="Text to copy">
    <ui:clipboard.label>Copy to clipboard</ui:clipboard.label>
    <ui:clipboard.control>
        <ui:clipboard.input />
        <ui:clipboard.trigger>
            <ui:clipboard.indicator state="idle">Copy</ui:clipboard.indicator>
            <ui:clipboard.indicator state="copied">Copied!</ui:clipboard.indicator>
        </ui:clipboard.trigger>
    </ui:clipboard.control>
</ui:clipboard.root>
```

## Examples

### Copy Button Only

Display only a copy button without the input field.

```html
<ui:clipboard.root value="https://example.com/share/abc123">
    <ui:clipboard.trigger>
        <ui:clipboard.indicator state="idle"> Copy Link </ui:clipboard.indicator>
        <ui:clipboard.indicator state="copied"> Link Copied! </ui:clipboard.indicator>
    </ui:clipboard.trigger>
</ui:clipboard.root>
```

### Custom Timeout

Set a custom duration for how long the "copied" state is shown.

```html
<ui:clipboard.root value="Copy me!" timeout="5000">
    <ui:clipboard.control>
        <ui:clipboard.input />
        <ui:clipboard.trigger>
            <ui:clipboard.indicator state="idle">Copy</ui:clipboard.indicator>
            <ui:clipboard.indicator state="copied">Copied for 5 seconds!</ui:clipboard.indicator>
        </ui:clipboard.trigger>
    </ui:clipboard.control>
</ui:clipboard.root>
```

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
