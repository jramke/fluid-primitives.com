# Password Input

**A password field with a button that shows and hides what you typed.**

{% component: "ui:referenceButtons", arguments: { "name": "PasswordInput" } %}

{% component: "ui:componentExample", arguments: { "componentName": "PasswordInputExamples.simple", "withEntryFile": true } %}

## Features

- Toggles the password between hidden and plain text
- Keeps focus in the input when the visibility trigger is clicked, and leaves the trigger out of the tab order
- Hides the password again when its form is submitted or reset
- Sets `autocomplete` to `current-password` or `new-password` for browser autofill
- Keeps password managers away with `ignorePasswordManagers`
- Works with Field: takes its name, required, disabled, readonly and invalid state from it

## Anatomy

```html
<primitives:passwordInput.root>
    <primitives:passwordInput.label />
    <primitives:passwordInput.control>
        <primitives:passwordInput.input />
        <primitives:passwordInput.visibilityTrigger>
            <primitives:passwordInput.indicator />
        </primitives:passwordInput.visibilityTrigger>
    </primitives:passwordInput.control>
</primitives:passwordInput.root>
```

## Installation

{% component: "ui:installationSection", arguments: { "name": "PasswordInput" } %}

## Examples

### Use it in a sign-up form

Nest `ui:passwordInput.root` in `ui:field.root`. The password input takes `name`, `required`, `disabled`, `invalid` and `aria-describedby` from the field. Set `autoComplete` to `new-password`, so the browser offers to generate a strong password. Type fewer than 12 characters in the demo and leave the input to see the error.

{% component: "ui:componentExample", arguments: { "componentName": "PasswordInputExamples.withField" } %}

### Keep password managers away

Set `ignorePasswordManagers="{true}"` for a password that is not the visitor's own login, like the guest Wi-Fi password in the demo. 1Password, LastPass, Bitwarden, Dashlane and Proton Pass then leave the input alone. `defaultVisible="{true}"` shows the password when the page loads.

{% component: "ui:componentExample", arguments: { "componentName": "PasswordInputExamples.ignorePasswordManagers" } %}

### Swap the icons

Put one `passwordInput.indicator` per state into the visibility trigger. The one matching the current state is shown.

```html
<primitives:passwordInput.visibilityTrigger>
    <primitives:passwordInput.indicator
        state="{f:constant(name: 'Jramke\FluidPrimitives\Enum\PasswordInputIndicatorState::Hidden')}"
        >Show</primitives:passwordInput.indicator
    >
    <primitives:passwordInput.indicator
        state="{f:constant(name: 'Jramke\FluidPrimitives\Enum\PasswordInputIndicatorState::Visible')}"
        >Hide</primitives:passwordInput.indicator
    >
</primitives:passwordInput.visibilityTrigger>
```

### Localization

The labels of the visibility trigger ship in the language file and follow the site language. Pass `translations` to override them for one password input: `visibilityTriggerLabelHidden` is read while the password is hidden ("Show password"), `visibilityTriggerLabelVisible` while it is shown.

```html
<f:variable
    name="passwordTranslations"
    value="{
        visibilityTriggerLabelHidden: '{f:translate(key: \'LLL:EXT:my_extension/Resources/Private/Language/locallang.xlf:forms.pin.show\')}',
        visibilityTriggerLabelVisible: '{f:translate(key: \'LLL:EXT:my_extension/Resources/Private/Language/locallang.xlf:forms.pin.hide\')}'
    }"
/>

<ui:passwordInput.root translations="{passwordTranslations}"> ... </ui:passwordInput.root>
```

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "PasswordInput",
        "parts": [
            ["root", "Provides shared password input state and wraps all related parts. Renders a `<div>` element."],
            ["label", "Labels the password input. Renders a `<label>` element."],
            ["control", "Groups the input and the visibility trigger. Renders a `<div>` element."],
            ["input", "Provides the password input. Renders an `<input>` element."],
            ["visibilityTrigger", "Shows or hides the password. Renders a `<button>` element."],
            ["indicator", "Shows content for one visibility state. Renders a `<span>` element."]
        ]
    }
%}
