# Clipboard

**A component to copy text to the clipboard**

{% component: "ui:referenceButtons", arguments: { "name": "Clipboard" } %}

{% component: "ui:clipboard.examples.simple" %}

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
