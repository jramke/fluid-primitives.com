# Input

**A text input component that works with Field, with optional word count and live-region announcements.**

{% component: "ui:referenceButtons", arguments: { "name": "Input" } %}

{% component: "ui:componentExample", arguments: { "componentName": "Input.examples.simple", "withEntryFile": true } %}

## Features

- Works standalone or nested directly inside `ui:field.root` - no `field.control` wrapper needed
- Optional `transform` callback to sanitize/reformat the value as the user types, with cursor position preserved
- Optional `wordCount` part rendering a translatable "42 / 250 characters" style counter, driven by `maxLength`
- Optional `liveRegion` part that announces word count updates to screen readers via [@zag-js/live-region](https://zagjs.com), debounced so it doesn't spam assistive tech on every keystroke

## Installation

{% component: "ui:installationSection", arguments: { "name": "Input" } %}

## Examples

### With Field

Nest `ui:input.root` directly inside `ui:field.root` - it inherits `name`, `disabled`, `required`, `invalid` and `aria-describedby` automatically, the same way `ui:select`/`ui:numberInput` do.

{% component: "ui:componentExample", arguments: { "componentName": "Input.examples.withField" } %}

### With Word Count

Pass `maxLength` and add the `wordCount`/`liveRegion` parts wherever you want them - they don't need to be direct siblings of `input`.

{% component: "ui:componentExample", arguments: { "componentName": "Input.examples.wordCount" } %}

### With a Transform Callback

`transform` runs on every native `input` event, before the value is committed - return the value that should actually be written back to the input. Because a real function can't cross the PHP → client JSON boundary, this can only be set by constructing `Input` yourself in a custom entry file, rather than as a Fluid prop:

```typescript
import { mount } from 'fluid-primitives';
import { Input } from 'fluid-primitives/input';

mount('input', 'coupon-code', ({ props }) => {
    const input = new Input({
        ...props,
        transform: value => value.toUpperCase(),
    });

    input.init();
});
```

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Input",
        "parts": [
            ["root", "Provides shared input state and wraps all related parts. Renders a `<div>` element."],
            ["label", "Labels the input. Renders a `<label>` element."],
            ["input", "The editable input. Renders an `<input>` element."],
            ["wordCount", "Displays the character count, e.g. '42 / 250 characters'. Renders a `<span>` element."],
            ["liveRegion", "Announces word count updates to assistive technology. Renders a `<div>` element."]
        ]
    }
%}

## Anatomy

```html
<primitives:input.root>
    <primitives:input.label />
    <primitives:input.input />
    <primitives:input.wordCount />
    <primitives:input.liveRegion />
</primitives:input.root>
```
