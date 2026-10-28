# Textarea

**A multi-line text input that works with Field, with an optional character counter, screen reader announcements and submit on Enter.**

{% component: "ui:referenceButtons", arguments: { "name": "Textarea" } %}

{% component: "ui:componentExample", arguments: { "componentName": "TextareaExamples.simple", "withEntryFile": true } %}

## Features

- Works standalone or directly inside `ui:field.root`, no `field.control` wrapper
- The `ui` copy grows with its content through CSS `field-sizing: content`, see the [MDN docs](https://developer.mozilla.org/en-US/docs/Web/CSS/field-sizing) for browser support
- Optional `submitOn` prop to submit the nearest form on Enter or Cmd/Ctrl+Enter
- Optional `transform` callback to sanitize or reformat the value while typing, the cursor position is kept
- Optional `wordCount` part with a translatable "42 / 250 characters" counter, driven by `maxLength`
- `maxLengthBehavior` lets a paste arrive whole and shows that it is over `maxLength`, instead of cutting it off
- Counter updates are announced to screen readers, debounced by `announceDebounce` (600 ms by default). Pass `announce="{false}"` to turn this off

## Anatomy

```html
<primitives:textarea.root>
    <primitives:textarea.label />
    <primitives:textarea.textarea />
    <primitives:textarea.wordCount />
</primitives:textarea.root>
```

## Installation

{% component: "ui:installationSection", arguments: { "name": "Textarea" } %}

## Examples

### With field

Nest `ui:textarea.root` directly inside `ui:field.root`. The textarea takes `name`, `disabled`, `required`, `invalid` and `aria-describedby` from the field. Label it with the primitive's own `textarea.label`, which targets the textarea for you. The demo is a required bio field with helper and error text.

{% component: "ui:componentExample", arguments: { "componentName": "TextareaExamples.withField" } %}

### With word count

Pass `maxLength` and place the `wordCount` part anywhere inside `textarea.root`. The bio in the demo counts up to 280 characters.

{% component: "ui:componentExample", arguments: { "componentName": "TextareaExamples.wordCount" } %}

### Submit on Enter

Set `submitOn="{f:constant(name: 'Jramke\FluidPrimitives\Enum\TextareaSubmitOn::ModEnter')}"` to submit the surrounding form with Cmd/Ctrl+Enter while plain Enter inserts a newline. `TextareaSubmitOn::Enter` submits with plain Enter and inserts a newline with Shift+Enter. Wrap the textarea in `ui:form.root` or a native `<form>`: `submitOn` calls its `requestSubmit()`. Press Cmd/Ctrl+Enter in the demo to submit it.

{% component: "ui:componentExample", arguments: { "componentName": "TextareaSubmitOnEnterExample", "additionalFiles": {"TextareaSubmitOnEnterExample.ts": "EXT:docs/Resources/Private/Components/TextareaSubmitOnEnterExample/TextareaSubmitOnEnterExample.entry.ts"} } %}

### Allow typing past the limit

Set `maxLengthBehavior="{f:constant(name: 'Jramke\FluidPrimitives\Enum\MaxLengthBehavior::Warn')}"` to keep `maxLength` as a target instead of a wall. The browser cuts nothing off, the count switches to "Characters over the limit: 6" and the parts carry `data-over-limit`, next to `data-at-limit` for exactly `maxLength` characters and `data-max-length-behavior` to style `warn` apart from `invalid`. The form still submits. The demo is a product review that starts over its limit.

{% component: "ui:componentExample", arguments: { "componentName": "TextareaExamples.maxLengthWarn" } %}

Enforce the limit that matters on the server. To accept a few characters over, give the template a lower `maxLength` than the validator, for example `StringLength(maximum: 210)` behind `maxLength="200"`. A textarea submits each line break as two characters, so leave a few characters of room. `Warn` and `Invalid` count characters like `mb_strlen` and a `VARCHAR(n)` column do, so an emoji counts once. `Block` counts what the browser counts, UTF-16 units, so an emoji counts twice.

### Reject text past the limit

`MaxLengthBehavior::Invalid` works like `Warn` and also makes the surrounding field invalid with the `tooLong` validity, so a form does not submit until the text fits. The error shows when the user leaves the field, like any other constraint. Give `ui:field.errorText` a `match` of `ValidityMatch::TooLong` for your own message. Type past the limit in the demo and tab out.

{% component: "ui:componentExample", arguments: { "componentName": "TextareaExamples.maxLengthInvalid" } %}

### With a transform callback

`transform` runs on every `input` event and returns the value to write back to the textarea. A function can't be set from the template, so pass `transform` where you create `Textarea` in your own entry file. Type in lowercase and it turns uppercase as you type.

{% component: "ui:componentExample", arguments: { "componentName": "TextareaTransformExample", "additionalFiles": {"TextareaTransformExample.ts": "EXT:docs/Resources/Private/Components/TextareaTransformExample/TextareaTransformExample.entry.ts"} } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Textarea",
        "parts": [
            ["root", "Provides shared textarea state and wraps all related parts. Renders a `<div>` element."],
            ["label", "Labels the textarea. Renders a `<label>` element."],
            ["textarea", "The editable textarea. Renders a `<textarea>` element."],
            ["wordCount", "Displays the character count, e.g. '42 / 250 characters'. Renders a `<span>` element."]
        ]
    }
%}
