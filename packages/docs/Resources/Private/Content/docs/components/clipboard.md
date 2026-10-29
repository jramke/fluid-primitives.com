# Clipboard

**A button that copies a value to the clipboard, with an optional input that shows it.**

{% component: "ui:referenceButtons", arguments: { "name": "Clipboard" } %}

{% component: "ui:componentExample", arguments: { "componentName": "ClipboardExamples.simple", "withEntryFile": true } %}

## Features

- Copies text to the clipboard
- Switches to a copied state as visual feedback
- Custom duration of the copied state with `timeout` (3000 ms by default)

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

## Installation

{% component: "ui:installationSection", arguments: { "name": "Clipboard" } %}

## Examples

### Copy button on its own

Leave out the `input` for a copy button on its own. The demo copies a share link.

{% component: "ui:componentExample", arguments: { "componentName": "ClipboardExamples.copyButtonOnly" } %}

### Custom timeout

Set `timeout` in milliseconds to change how long the copied state stays. The button in the demo reads "Copied for 5 seconds!" for `timeout="5000"`.

{% component: "ui:componentExample", arguments: { "componentName": "ClipboardExamples.customTimeout" } %}

### Localization

The trigger labels for the idle and the copied state ship in the language file and follow the site language. Pass `translations` with one string per state to override them for one clipboard.

```html
<f:variable
    name="clipboardTranslations"
    value="{
        triggerLabelIdle: '{f:translate(key: \'LLL:EXT:my_extension/Resources/Private/Language/locallang.xlf:clipboard.copy\')}',
        triggerLabelCopied: '{f:translate(key: \'LLL:EXT:my_extension/Resources/Private/Language/locallang.xlf:clipboard.copied\')}'
    }"
/>

<ui:clipboard.root value="SUMMER24" translations="{clipboardTranslations}"> ... </ui:clipboard.root>
```

A client script passes a `triggerLabel` function of the copied state instead of the two strings.

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
