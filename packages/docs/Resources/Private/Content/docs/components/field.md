# Field

**A form field wrapper that provides accessible labeling, helper and error text, validation state and indicators for form inputs.**

{% component: "ui:referenceButtons", arguments: { "name": "Field", "skipZag": true } %}

{% component: "ui:componentExample", arguments: { "componentName": "FieldExamples.simple", "withEntryFile": true } %}

## Features

- Automatic label association with form controls
- Helper text and error text, linked to the control with the right ARIA attributes
- Reads the browser's own constraint validation: `required`, `pattern`, `type="email"`, `min`/`max`, `minlength`/`maxlength` and more decide whether a field is valid, and error texts can be narrowed to the constraint that failed
- Custom validation per field, synchronous or asynchronous, next to the schema or callback validation of the [Form](/docs/components/form) - all of them end up in the same place
- Validation modes decide when errors become visible, and an error that is showing always follows the user while they fix it
- State flags for touched, dirty, filled, focused, valid, invalid, disabled, required and read-only, mirrored as data attributes on every part
- Indicators for the required, invalid, valid and validating state
- Disabled by a surrounding [Fieldset](/docs/components/fieldset) or `<fieldset disabled>`
- Works with all form-related primitives (Checkbox, Select, RadioGroup, etc.) and native inputs

## Installation

{% component: "ui:installationSection", arguments: { "name": "Field" } %}

## Examples

### Required Field

Mark a field as required. An empty required field stays quiet until the user has edited it or the form is submitted.

{% component: "ui:componentExample", arguments: { "componentName": "FieldExamples.required" } %}

### With Helper Text

Add helpful text below the input. It is linked to the control with `aria-describedby`.

{% component: "ui:componentExample", arguments: { "componentName": "FieldExamples.withHelperText" } %}

### Invalid State

Indicate that the field has an error and display an error message.

{% component: "ui:componentExample", arguments: { "componentName": "FieldExamples.invalid" } %}

### Disabled Field

Disable the entire field.

{% component: "ui:componentExample", arguments: { "componentName": "FieldExamples.disabled" } %}

### With Checkbox

Use with the Checkbox component.

{% component: "ui:componentExample", arguments: { "componentName": "FieldExamples.withCheckbox" } %}

### Native Validation

The field reads the `ValidityState` of its control, so the constraints you already know are validation rules: `required` (the prop of the field, not an attribute), `type="email"` and the like on the input, and `pattern`, `min`, `max`, `step`, `minlength` and `maxlength`, which you pass to the input part as plain attributes.

The `form` renders `novalidate`, so the browser never shows its own bubble. Instead, an error text without a `match` shows the message of the failed constraint, in the language of the browser. Give it a `match` to show your own text for one constraint, e.g. `valueMissing`, `typeMismatch`, `patternMismatch`, `tooShort`, `tooLong`, `rangeUnderflow`, `rangeOverflow`, `stepMismatch`, `badInput` or `customError`. An error text with a `match` stays hidden until exactly that constraint failed.

{% component: "ui:componentExample", arguments: { "componentName": "FieldExamples.nativeValidation" } %}

{% component: "ui:alert", arguments: {"title": "Where the value comes from", "text": "A field validates the value of the primitive inside it, not only native inputs: a required Select, Combobox, Checkbox, RadioGroup or CheckboxGroup counts as missing while nothing is chosen. The other constraints come from the native control, so they apply to inputs and textareas.", "variant": "info"} %}

### Validation Mode

`validationMode` decides when a field validates and shows its errors:

- `onBlur` (the default) validates when the user leaves a field they have edited. Tabbing through untouched fields stays quiet.
- `onSubmit` shows errors after the first submit attempt, and from then on as the user types.
- `onChange` validates on every change.

In every mode, an error that is already showing is validated again on every change, so the message goes away as soon as the user has fixed the value, instead of waiting for the next blur. A submit validates all fields, shows every error and moves the focus to the first invalid one.

{% component: "ui:componentExample", arguments: { "componentName": "FieldExamples.validationMode" } %}

### Indicators

An `indicator` shows its content while the field is in one state, and stays hidden otherwise. Its `type` is one of `required`, `invalid`, `valid` and `validating`. `valid` appears once the field has been validated and passed.

{% component: "ui:componentExample", arguments: { "componentName": "FieldExamples.indicators" } %}

### Async Validation

A field can run its own check, for example to ask the server whether a username is still free. Pass a `validate` function that returns a message (or a list of messages) to mark the field invalid, and `null` to accept the value. It may return a promise.

A function cannot travel through the server-rendered markup, so pass it from TypeScript: render the field with `controlled="{true}"`, and mount it yourself. While the promise is pending the field is `validating` (use the `validating` indicator), a result for a value the user has changed meanwhile is dropped, and a submit waits for it.

`validate` receives the `value` and a snapshot of the native `validity`. It runs at the same points that make an error visible (see the validation mode), not on every keystroke, so it is a good place for a request.

{% component: "ui:componentExample", arguments: { "componentName": "FieldExamples.asyncValidation", "additionalFiles": {"AsyncValidation.entry.ts": "EXT:docs/Resources/Private/Components/ui/FieldExamples/AsyncValidation.entry.ts"} } %}

Use `updateProps({ validate })` to replace the function later. The schema or callback of the `Form` and the errors the server sent back (422 or from `onSubmit`) do not need any of this, they reach the field on their own and are shown in the same error text.

### Linked Fields

A field can react to a _different_ field's value changing via the `listenTo` prop - names of sibling fields within the same form to watch. Two things happen whenever a listened-to field changes its value: a `fluid-primitives:field:dependencychange` event is dispatched on the root element of this field, for any imperative DOM reaction you need, and this field is treated as if its own value had changed. It validates again under the same rules as when its own user types: immediately if it shows an error (or its `validationMode` is `onChange`, or the form was submitted before), otherwise at its next blur or submit.

`FieldHandle` (what both `form.api.getField(name)` and a `Field` instance's own `.api` return) exposes `addDependencyChangeListener(callback)` for the first half - it registers `callback` and returns a function that removes it again, the same "call it to start listening, get a cleanup function back" shape as `addEventListener`/`Machine.subscribe`, rather than a config-style `onX` prop you'd set once:

```typescript
const studentIdField = form.api.getField('studentId')!;

studentIdField.addDependencyChangeListener(({ dependencies }) => {
    const show = dependencies.ticketType === 'student';
    studentIdField.getRootEl()!.hidden = !show;
    studentIdField.setDisabled(!show);
});
```

See the [Complete Example: Event Registration](/docs/core-concepts/forms#complete-example-event-registration) in the Forms guide for this exact pattern used to show/hide a `studentId` field based on `ticketType`, in place of recomputing it inside the form's own `render` callback on every field change.

The classic case is a password confirmation field - `passwordConfirm` listens to `password`, so changing `password` validates `passwordConfirm` again, instead of leaving a stale result:

```html
<ui:field.root name="password">
    <ui:input.root type="password">
        <ui:input.label>Password</ui:input.label>
        <ui:input.input />
    </ui:input.root>
    <ui:field.errorText />
</ui:field.root>

<ui:field.root name="passwordConfirm" listenTo="{0: 'password'}">
    <ui:input.root type="password">
        <ui:input.label>Confirm password</ui:input.label>
        <ui:input.input />
    </ui:input.root>
    <ui:field.errorText />
</ui:field.root>
```

No consumer JavaScript is needed for the _re-triggering_ itself - `listenTo` handles that. You still need to decide what "valid" means, though, and a manual `validation` callback (see [Manual Client-Side Validation](/docs/core-concepts/forms#manual-client-side-validation)) fits better here than a single schema: checking each field independently means `passwordConfirm`'s mismatch error can appear even while `password` hasn't yet satisfied its own length requirement, whereas a single Zod schema with `.refine()` would silently skip that cross-field check until the base shape (including `password`'s own `min(8)`) validates first:

```typescript
validation: ({ values }) => {
    const errors: Record<string, { messages: string[] }> = {};

    const password = values.get('password');
    if (typeof password !== 'string' || password.length < 8) {
        errors.password = { messages: ['Password must be at least 8 characters'] };
    }

    const passwordConfirm = values.get('passwordConfirm');
    if (passwordConfirm !== null && passwordConfirm !== password) {
        errors.passwordConfirm = { messages: ['Passwords do not match'] };
    }

    return errors;
},
```

{% component: "ui:componentExample", arguments: { "componentName": "PasswordConfirmation", "additionalFiles": {"PasswordConfirmation.entry.ts": "EXT:docs/Resources/Private/Components/PasswordConfirmation/PasswordConfirmation.entry.ts"} } %}

{% component: "ui:alert", arguments: {"title": "Multiple dependencies", "text": "listenTo accepts more than one name, e.g. listenTo=\"{0: 'a', 1: 'b'}\" - the dependencychange event's dependencies object then carries every listened field's current value, keyed by name.", "variant": "info"} %}

## State and Data Attributes

Every part of a field renders the state of the field as data attributes, so a label, an indicator or an error text can be styled without any JavaScript. The server renders the ones that follow from the props (`data-disabled`, `data-invalid`, `data-required`, `data-readonly`), the others are set once the field is hydrated.

| Attribute       | Present when                                                                                               |
| --------------- | ---------------------------------------------------------------------------------------------------------- |
| `data-disabled` | The field is disabled, or it is inside a disabled fieldset                                                 |
| `data-readonly` | The field is read-only                                                                                     |
| `data-required` | The field is required                                                                                      |
| `data-invalid`  | The field is invalid: a native constraint failed, `validate`, the form or the server reported an error     |
| `data-valid`    | The field has been validated and passed                                                                    |
| `data-touched`  | The user has left the field at least once                                                                  |
| `data-dirty`    | The value differs from the value the field started with. It goes away again when the user reverts the edit |
| `data-filled`   | The field has a value                                                                                      |
| `data-focus`    | Focus is inside the field                                                                                  |

The same flags are available from TypeScript on `field.api` (and on the `FieldHandle` of `form.api.getField(name)`): `touched`, `dirty`, `filled`, `focused`, `valid`, `invalid`, `validating`, `disabled`, `required`, `readOnly` and the `errors` list, together with `validate()`, `clearErrors()` and `reset()`. A `fluid-primitives:field:valuechange` event bubbles from the root of the field whenever its value settled on a new one, 100 ms after the last keystroke by default.

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Field",
        "skipZag": true,
        "parts": [
            ["root", "Provides shared field state for labels, helper text, errors, indicators and controls. Renders a `<div>` element."],
            ["label", "Labels the associated form control. Renders a `<label>` element."],
            ["control", "Wraps the slotted form control and wires up shared field attributes. Renders the element defined by the `asChild` prop."],
            ["helperText", "Displays help or supporting text for the field. Renders a `<div>` element."],
            ["errorText", "Displays the validation message of the field, or the text of its `match` when one constraint failed. Renders a `<div>` element."],
            ["indicator", "Shows its content while the field is in the state given by `type`: `required`, `invalid`, `valid` or `validating`. Renders a `<span>` element."]
        ]
    }
%}

## Anatomy

```html
<primitives:field.root>
    <primitives:field.label />
    <primitives:field.control asChild="{true}">
        <!-- Your form input here -->
    </primitives:field.control>
    <primitives:field.indicator type="required" />
    <primitives:field.helperText />
    <primitives:field.errorText />
</primitives:field.root>
```
