---
preview: true
---

# Button

**A native button with a loading state and a disabled state that can keep focus.**

{% component: "ui:referenceButtons", arguments: { "name": "Button" } %}

{% component: "ui:componentExample", arguments: { "componentName": "ButtonExamples.simple" } %}

## Features

- Renders a native `<button>` and sets `type="button"` by default, so a button inside a form never submits it by accident - pass `type="submit"` for the submit button
- `disabled` and `loading` states, a loading button is disabled too
- `focusableWhenDisabled` keeps a disabled button in the tab order, so focus is not lost when it gets disabled
- `indicator` parts show and hide content per state, for loading spinners and text changes
- Works as the `asChild` target of triggers like `ui:collapsible.trigger` and as the building block of your own components

## Anatomy

Unlike most components the button has no `root` part. It is a single element with optional parts, so the folder-named file is its root (`<ui:button>`), see [Root or the Folder Name](/docs/core-concepts/file-structure#content-root-or-the-folder-name).

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

### Disabled

A disabled button gets the native `disabled` attribute. Browsers take it out of the tab order, and a focused button loses focus the moment it is disabled.

{% component: "ui:componentExample", arguments: { "componentName": "ButtonExamples.disabled" } %}

### Focusable When Disabled

With `focusableWhenDisabled` the button gets `aria-disabled="true"` instead of the `disabled` attribute and stays focusable. Clicks and the Enter and Space keys are ignored in the capture phase, before any other listener of the button runs, so it still never submits a form or opens the dialog it triggers. Press <kbd>Tab</kbd> to compare the two buttons below.

{% component: "ui:componentExample", arguments: { "componentName": "ButtonExamples.focusableWhenDisabled" } %}

### Loading States

For a button that starts loading after it was activated, set `focusableWhenDisabled` so focus stays on it while it is disabled.

Some browser and screen reader combinations do not reliably announce a change of the text inside a focused button. Make the changing text the explicit accessible name instead: wrap the indicators in an element with an `id` and point `aria-labelledby` at it.

Each `indicator` is only visible in its `state`: `idle`, `loading` or `disabled`. `loading` wins over `disabled`, and a state without an indicator renders nothing, so add one for every state the button can be in. Decorative content like a spinner needs `aria-hidden="true"`.

The server renders the state, and on the client you change it with `api.setLoading()` and `api.setDisabled()`. Mount the button yourself with `autoMount="{false}"` and a `rootId`, as below. `ui:button` only loads its own auto-mounting entry file for a button with `focusableWhenDisabled`, because a plain button needs no JavaScript:

{% component: "ui:componentExample", arguments: { "componentName": "ButtonExamples.loading", "additionalFiles": {"Loading.entry.ts": "EXT:docs/Resources/Private/Components/ui/ButtonExamples/Loading.entry.ts"} } %}

For a submit button of a form, pass `form.api.isSubmitting` as `loading`, see [Forms](/docs/core-concepts/forms#content-submit-button).

### As a Trigger

The button renders a plain `<button>` and does not set `data-state` or an `id`, so a trigger can merge its own attributes onto it with `asChild`: `aria-expanded` and `data-state` come from the collapsible, `type` and the disabled handling from the button.

{% component: "ui:componentExample", arguments: { "componentName": "ButtonExamples.collapsibleTrigger" } %}

### Custom Components

The button is meant to be a part of larger components of your own. Put it into the template, give it a `rootId` derived from your component's, and mount it from your component's entry file. The component then drives the button from its own logic, here a backup that runs for a moment.

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
