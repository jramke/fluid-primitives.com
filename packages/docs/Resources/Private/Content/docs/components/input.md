# Input

**A text input that works with Field, with an optional character counter and screen reader announcements.**

{% component: "ui:referenceButtons", arguments: { "name": "Input" } %}

{% component: "ui:componentExample", arguments: { "componentName": "InputExamples.simple", "withEntryFile": true } %}

## Features

- Works standalone or directly inside `ui:field.root`, no `field.control` wrapper
- Optional `transform` callback to sanitize or reformat the value while typing, the cursor position is kept
- Optional `wordCount` part with a translatable "42 / 250 characters" counter, driven by `maxLength`
- `maxLengthBehavior` lets a paste arrive whole and shows that it is over `maxLength`, instead of cutting it off
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

### Allow typing past the limit

Set `maxLengthBehavior="{f:constant(name: 'Jramke\FluidPrimitives\Enum\MaxLengthBehavior::Warn')}"` to keep `maxLength` as a target instead of a wall. The browser cuts nothing off, the count switches to "6 characters too many" and the parts carry `data-over-limit`, next to `data-at-limit` for exactly `maxLength` characters and `data-max-length-behavior` to style `warn` apart from `invalid`. The form still submits. The demo is a meta description that starts over its limit.

{% component: "ui:componentExample", arguments: { "componentName": "InputExamples.maxLengthWarn" } %}

Enforce the limit that matters on the server. To accept a few characters over, give the template a lower `maxLength` than the validator, for example `StringLength(maximum: 210)` behind `maxLength="200"`. `Warn` and `Invalid` count characters like `mb_strlen` and a `VARCHAR(n)` column do, so an emoji counts once. `Block` counts what the browser counts, UTF-16 units, so an emoji counts twice.

### Reject text past the limit

`MaxLengthBehavior::Invalid` works like `Warn` and also makes the surrounding field invalid with the `tooLong` validity, so a form does not submit until the text fits. The error shows when the user leaves the field, like any other constraint. Give `ui:field.errorText` a `match` of `ValidityMatch::TooLong` for your own message. Type past the limit in the demo and tab out.

{% component: "ui:componentExample", arguments: { "componentName": "InputExamples.maxLengthInvalid" } %}

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
