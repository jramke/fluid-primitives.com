# Clipboard

**A component to copy text to the clipboard.**

{% component: "ui:referenceButtons", arguments: { "name": "Clipboard" } %}

{% component: "ui:componentExample", arguments: { "componentName": "Clipboard.examples.simple", "withEntryFile": true } %}

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

### Localization

Default clipboard trigger labels are shipped via XLF and follow the current Site Language. For per-template overrides, pass translated strings through the `translations` prop. Set a translation entry to `{false}` or an empty string to omit the corresponding `aria-label`.

Note that Zag.js uses a function for the trigger label to allow dynamic labels based on the copied state. Fluid Primitives simplifies this by accepting static strings for both states, which are then merged into the appropriate function internally.

```html
<f:variable
    name="clipboardTranslations"
    value="{
        triggerLabelIdle: '{f:translate(key: \'LLL:EXT:site_package/Resources/Private/Language/locallang.xlf:clipboard.copy\')}',
        triggerLabelCopied: '{f:translate(key: \'LLL:EXT:site_package/Resources/Private/Language/locallang.xlf:clipboard.copied\')}'
    }"
/>

<ui:clipboard.root value="Copy me" translations="{clipboardTranslations}"> ... </ui:clipboard.root>
```

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Clipboard",
        "parts": [
            ["root", "Provides the clipboard state and shared context for the composed parts. Renders a `<div>` element."],
            ["label", "Labels the clipboard input. Renders a `<label>` element."],
            ["control", "Groups the input and trigger into a single layout container. Renders a `<div>` element."],
            ["input", "Displays the text value that can be copied. Renders an `<input>` element."],
            ["trigger", "Copies the current value to the clipboard when activated. Renders a `<button>` element."],
            ["indicator", "Displays UI for a specific clipboard state like `idle` or `copied`. Renders a `<span>` element."]
        ]
    }
%}

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
