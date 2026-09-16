# Textarea

**A multi-line text input that works with Field, with optional word count, live-region announcements, and submit-on-Enter.**

{% component: "ui:referenceButtons", arguments: { "name": "Textarea" } %}

{% component: "ui:componentExample", arguments: { "componentName": "Textarea.examples.simple", "withEntryFile": true } %}

## Features

- Works standalone or nested directly inside `ui:field.root` - no `field.control` wrapper needed
- Auto-resizes to fit its content via CSS `field-sizing: content` - no `@zag-js/auto-resize` or JS involved, see the [MDN docs](https://developer.mozilla.org/en-US/docs/Web/CSS/field-sizing) for browser support
- Optional `submitOn` prop to submit the nearest form on Enter or Cmd/Ctrl+Enter instead of inserting a newline
- Optional `transform` callback to sanitize/reformat the value as the user types, with cursor position preserved
- Optional `wordCount` part rendering a translatable "42 / 250 characters" style counter, driven by `maxLength`
- Optional `liveRegion` part that announces word count updates to screen readers via [@zag-js/live-region](https://zagjs.com), debounced so it doesn't spam assistive tech on every keystroke

## Installation

{% component: "ui:installationSection", arguments: { "name": "Textarea" } %}

## Examples

### With Field

Nest `ui:textarea.root` directly inside `ui:field.root` - it inherits `name`, `disabled`, `required`, `invalid` and `aria-describedby` automatically, the same way `ui:input`/`ui:select` do.

{% component: "ui:componentExample", arguments: { "componentName": "Textarea.examples.withField" } %}

### With Word Count

Pass `maxLength` and add the `wordCount`/`liveRegion` parts wherever you want them - they don't need to be direct siblings of `textarea`.

{% component: "ui:componentExample", arguments: { "componentName": "Textarea.examples.wordCount" } %}

### Submit on Enter

Pass `submitOn="{f:constant(name: 'Jramke\FluidPrimitives\Enum\TextareaSubmitOn::ModEnter')}"` to submit the nearest `<form>` on Cmd/Ctrl+Enter instead of inserting a newline - plain Enter still inserts a newline. Use `TextareaSubmitOn::Enter` for the opposite: plain Enter submits, Shift+Enter inserts a newline. Wrap the textarea in `ui:form.root` (or any native `<form>`) for the submit to actually go anywhere - `submitOn` just calls `closest('form')?.requestSubmit()`.

{% component: "ui:componentExample", arguments: { "componentName": "Textarea.examples.submitOnEnter" } %}

### With a Transform Callback

`transform` runs on every native `input` event, before the value is committed - return the value that should actually be written back to the textarea. Because a real function can't cross the PHP → client JSON boundary, this can only be set by constructing `Textarea` yourself in a custom entry file, rather than as a Fluid prop:

```typescript
import { mount } from 'fluid-primitives';
import { Textarea } from 'fluid-primitives/textarea';

mount('textarea', 'comment', ({ props }) => {
    const textarea = new Textarea({
        ...props,
        transform: value => value.replace(/\s+/g, ' '),
    });

    textarea.init();
});
```

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Textarea",
        "parts": [
            ["root", "Provides shared textarea state and wraps all related parts. Renders a `<div>` element."],
            ["label", "Labels the textarea. Renders a `<label>` element."],
            ["textarea", "The editable textarea. Renders a `<textarea>` element."],
            ["wordCount", "Displays the character count, e.g. '42 / 250 characters'. Renders a `<span>` element."],
            ["liveRegion", "Announces word count updates to assistive technology. Renders a `<div>` element."]
        ]
    }
%}

## Anatomy

```html
<primitives:textarea.root>
    <primitives:textarea.label />
    <primitives:textarea.textarea />
    <primitives:textarea.wordCount />
    <primitives:textarea.liveRegion />
</primitives:textarea.root>
```
