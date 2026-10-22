---
preview: true
---

# Button

**A native button with a loading state and a disabled state that can keep focus.**

{% component: "ui:referenceButtons", arguments: { "name": "Button" } %}

{% component: "ui:componentExample", arguments: { "componentName": "ButtonExamples.simple" } %}

## Features

- Native `<button>` with `type="button"` by default
- `disabled` and `loading` states
- `focusableWhenDisabled` keeps a disabled button focusable
- `indicator` parts show content per state
- Works as the `asChild` target of triggers
- Building block for your own components

## Anatomy

The button is a single element with optional parts, so the file named after its folder is the root (`<ui:button>`). See [Root or the Folder Name](/docs/core-concepts/file-structure#content-root-or-the-folder-name).

```html
<primitives:button>
    <primitives:button.indicator state="idle" />
    <primitives:button.indicator state="loading" />
    <primitives:button.indicator state="disabled" />
</primitives:button>
```

## Installation

{% component: "ui:installationSection", arguments: { "name": "Button" } %}

## Examples

### Disable a button

Set `disabled` to render the native `disabled` attribute. Browsers skip a disabled button in the tab order, and a focused button loses focus the moment it is disabled.

{% component: "ui:componentExample", arguments: { "componentName": "ButtonExamples.disabled" } %}

### Keep a disabled button focusable

Set `focusableWhenDisabled` to render `aria-disabled="true"` instead of `disabled`, so the button stays in the tab order. It swallows clicks and the `Enter` and `Space` keys before any other listener runs, so a disabled submit button leaves its form alone and a disabled trigger leaves its dialog closed. Press `Tab` to compare the two buttons below.

{% component: "ui:componentExample", arguments: { "componentName": "ButtonExamples.focusableWhenDisabled" } %}

### Loading states

Click the button below: it shows a spinner and the label "Submitting" for four seconds. Each `indicator` is visible in its own `state` (`idle`, `loading` or `disabled`) and hidden in the others, and `loading` takes precedence over `disabled`.

{% component: "ui:componentExample", arguments: { "componentName": "ButtonExamples.loading", "additionalFiles": {"Loading.entry.ts": "EXT:docs/Resources/Private/Components/ui/ButtonExamples/Loading.entry.ts"} } %}

Change the state from your entry file with `api.setLoading()` or `api.setDisabled()`. For that, mount the button yourself with `autoMount="{false}"` and a `rootId`, as in the demo. Then:

- `focusableWhenDisabled` - keeps focus on the button while it loads.
- `aria-labelledby` - points at the element that wraps the label indicators, so the new label is announced. Some browser and screen reader combinations do not reliably announce text changes inside a focused button.
- One `indicator` per state - a state without one renders nothing, so its content disappears.
- `aria-hidden="true"` - on decorative content like the spinner.

In a form, set `type="submit"` and pass `form.api.isSubmitting` as `loading`, see [Forms](/docs/core-concepts/forms#content-submit-button).

### Use a button as a trigger

Put a `ui:button` inside a trigger like `ui:collapsible.trigger` and set `asChild` on the trigger. The trigger adds `aria-expanded` and `data-state`, the button keeps its `type` and disabled handling.

{% component: "ui:componentExample", arguments: { "componentName": "ButtonExamples.collapsibleTrigger" } %}

### Build your own component with a focusable button

Click "Back up now" in the card below: the button shows a spinner for two seconds, then the card updates its status line. The card's entry file mounts the button with a `rootId` derived from its own and drives it with `api.setLoading()`.

{% component: "ui:componentExample", arguments: { "componentName": "ButtonExamples.backupCard", "additionalFiles": {"BackupCard.entry.ts": "EXT:docs/Resources/Private/Components/ui/ButtonExamples/BackupCard.entry.ts"} } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Button",
        "parts": [
            ["", "The button. Renders a `<button>` element."],
            ["indicator", "Shows its content only while the button is in its `state`. Renders a `<span>` element."]
        ]
    }
%}
