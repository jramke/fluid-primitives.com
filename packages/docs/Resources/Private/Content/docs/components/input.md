# Input

**A text input that works with Field, with an optional character counter and screen reader announcements.**

{% component: "ui:referenceButtons", arguments: { "name": "Input" } %}

{% component: "ui:componentExample", arguments: { "componentName": "InputExamples.simple", "withEntryFile": true } %}

## Features

- Works standalone or directly inside `ui:field.root`, no `field.control` wrapper
- Optional `transform` callback to sanitize or reformat the value while typing, the cursor position is kept
- Optional `wordCount` part with a translatable "42 / 250 characters" counter, driven by `maxLength`
- Counter updates are announced to screen readers, debounced by `announceDebounce` (600 ms by default). Pass `announce="{false}"` to turn this off

## Anatomy

```html
<primitives:input.root>
    <primitives:input.label />
    <primitives:input.input />
    <primitives:input.wordCount />
</primitives:input.root>
```

## Installation

{% component: "ui:installationSection", arguments: { "name": "Input" } %}

## Examples

### With field

Nest `ui:input.root` directly inside `ui:field.root`. The input takes `name`, `disabled`, `required`, `invalid` and `aria-describedby` from the field. Label it with the primitive's own `input.label`, which targets the input for you. The demo is a required email field with helper and error text.

{% component: "ui:componentExample", arguments: { "componentName": "InputExamples.withField" } %}

### With word count

Pass `maxLength` and place the `wordCount` part anywhere inside `input.root`. The bio in the demo counts up to 140 characters.

{% component: "ui:componentExample", arguments: { "componentName": "InputExamples.wordCount" } %}

### With a transform callback

`transform` runs on every `input` event and returns the value to write back to the input. A function can't be set from the template, so pass `transform` where you create `Input` in your own entry file. Type a coupon code in lowercase and it turns uppercase as you type.

{% component: "ui:componentExample", arguments: { "componentName": "TransformExample", "additionalFiles": {"TransformExample.ts": "EXT:docs/Resources/Private/Components/TransformExample/TransformExample.entry.ts"} } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Input",
        "parts": [
            ["root", "Provides shared input state and wraps all related parts. Renders a `<div>` element."],
            ["label", "Labels the input. Renders a `<label>` element."],
            ["input", "The editable input. Renders an `<input>` element."],
            ["wordCount", "Displays the character count, e.g. '42 / 250 characters'. Renders a `<span>` element."]
        ]
    }
%}
