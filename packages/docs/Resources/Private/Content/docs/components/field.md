# Field

**A form field wrapper that provides accessible labeling, helper and error text, validation state and indicators for form inputs.**

{% component: "ui:referenceButtons", arguments: { "name": "Field" } %}

{% component: "ui:componentExample", arguments: { "componentName": "FieldExamples.simple", "withEntryFile": true } %}

## Features

- Label, helper text and error text linked to the control with the right ARIA attributes
- Validates with the browser's constraints (`required`, `pattern`, `type="email"`, `min`/`max`, `minlength`/`maxlength`), your own `validate` function and the schema or callback of the [Form](/docs/components/form)
- Validation modes decide when errors show
- State as data attributes on every part: touched, dirty, filled, focused, valid, invalid, disabled, required and read-only
- Indicators for the required, invalid, valid and validating state
- Disabled by a surrounding [Fieldset](/docs/components/fieldset) or `<fieldset disabled>`
- Works with the form primitives (Checkbox, Select, RadioGroup, ...) and native inputs

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

## Installation

{% component: "ui:installationSection", arguments: { "name": "Field" } %}

## Examples

### Mark a field as required

An empty required field stays quiet until the user has edited it or submitted the form.

{% component: "ui:componentExample", arguments: { "componentName": "FieldExamples.required" } %}

### Add helper text

Helper text sits below the input and is linked to it with `aria-describedby`.

{% component: "ui:componentExample", arguments: { "componentName": "FieldExamples.withHelperText" } %}

### Show an error

Set `invalid` to show the error text, for example for a rule your backend decides.

{% component: "ui:componentExample", arguments: { "componentName": "FieldExamples.invalid" } %}

### Disable a field

`disabled` on the field disables the control inside it.

{% component: "ui:componentExample", arguments: { "componentName": "FieldExamples.disabled" } %}

### Require a checkbox

A required checkbox counts as missing until it is checked, for example "I accept the terms and conditions". The same goes for a required Select, Combobox, RadioGroup or CheckboxGroup while nothing is chosen.

{% component: "ui:componentExample", arguments: { "componentName": "FieldExamples.withCheckbox" } %}

### Validate with native constraints

The field reads the `ValidityState` of its control, so the constraints you already know are the rules: `required` as a prop of the field, and `type="email"`, `pattern`, `min`, `max`, `step`, `minlength` and `maxlength` as plain attributes on the input part. The constraints other than `required` come from a native input or textarea.

The form renders `novalidate`, which hands the display of errors to your error texts. An error text without a `match` shows the browser's message for the failed constraint. Give it a `match` to show your own text for exactly that constraint: `valueMissing`, `typeMismatch`, `patternMismatch`, `tooShort`, `tooLong`, `rangeUnderflow`, `rangeOverflow`, `stepMismatch`, `badInput` or `customError`.

{% component: "ui:componentExample", arguments: { "componentName": "FieldExamples.nativeValidation" } %}

### Validation mode

`validationMode` decides when a field validates and shows its errors:

- `onBlur` (default) - validates when the user leaves a field they have edited. Tabbing through untouched fields stays quiet.
- `onSubmit` - shows errors after the first submit attempt, and from then on while the user types.
- `onChange` - validates on every change.

Once an error shows, it is validated again on every change, so the message goes away as soon as the value is fixed. A submit validates all fields, shows every error and moves the focus to the first invalid one. While a mouse button or finger is down, error texts and indicators wait for the release. Keyboard users get them right away.

{% component: "ui:componentExample", arguments: { "componentName": "FieldExamples.validationMode" } %}

### Show state with indicators

An `indicator` shows its content while the field is in the state given by its `type`: `required`, `invalid`, `valid` or `validating`. `valid` appears once the field has been validated and passed.

{% component: "ui:componentExample", arguments: { "componentName": "FieldExamples.indicators" } %}

### Async validation

Ask the server whether a username is still free: pass a `validate` function that returns a message (or a list of messages) to mark the field invalid, and `null` to accept the value. It can return a promise. While the request runs, the `validating` indicator shows and a submit waits for the result. A result for a value the user has changed meanwhile is dropped.

A function cannot be rendered into markup, so pass `validate` from TypeScript: render the field with `autoMount="{false}"` and mount it in your entry file. `validate` receives the `value` and a snapshot of the native `validity`. It runs where the validation mode lets errors show (on blur by default), which makes it a good place for a request.

{% component: "ui:componentExample", arguments: { "componentName": "FieldExamples.asyncValidation", "additionalFiles": {"AsyncValidation.entry.ts": "EXT:docs/Resources/Private/Components/ui/FieldExamples/AsyncValidation.entry.ts"} } %}

Replace the function later with `updateProps({ validate })`. Errors from the [Form](/docs/components/form) schema or callback and from the server (a 422 or `onSubmit`) reach the field on their own and show in the same error text.

### Confirm a password

Add `listenTo="{0: 'password'}"` to the `passwordConfirm` field. It names the sibling fields to watch, so when `password` changes, `passwordConfirm` validates again and its result stays current.

Put both rules in a `validation` callback, see [Manual Client-Side Validation](/docs/core-concepts/forms#content-manual-client-side-validation). A Zod `.refine()` on the whole schema runs after every field passes its own rule, so the mismatch error would wait for the length rule.

{% component: "ui:componentExample", arguments: { "componentName": "PasswordConfirmation", "additionalFiles": {"PasswordConfirmation.entry.ts": "EXT:docs/Resources/Private/Components/PasswordConfirmation/PasswordConfirmation.entry.ts"} } %}

### Show a field depending on another field

Whenever a field named in `listenTo` changes, the root of the listening field dispatches `fluid-primitives:field:dependencychange`. `addDependencyChangeListener()` on the field handle subscribes to it and returns a function that removes the listener. `listenTo` takes several names, e.g. `listenTo="{0: 'arrival', 1: 'departure'}"`.

This shows a `studentId` field with `listenTo="{0: 'ticketType'}"` when the ticket type is student:

```typescript
const studentId = form.api.getField('studentId')!;

studentId.addDependencyChangeListener(({ values }) => {
    const show = values.get('ticketType') === 'student';
    studentId.getRootEl()!.hidden = !show;
    studentId.setDisabled(!show); // a disabled field is neither validated nor submitted
});
```

The callback receives `dependencies` (the value of each listened field, keyed by name) and `values`, the `FormValues` of the whole form. The [event registration example](/docs/core-concepts/forms#content-complete-example-event-registration) in the Forms guide shows it in a working form.

### Style a field by its state

Every part renders the state of the field as data attributes, so a label, an indicator or an error text can be styled with CSS alone. The [API reference](#content-api-reference) lists them per part.

From TypeScript, `field.api` and the `FieldHandle` of `form.api.getField(name)` expose the same flags: `touched`, `dirty`, `filled`, `focused`, `valid`, `invalid`, `validating`, `disabled`, `required`, `readOnly` and `errors`, together with `validate()`, `clearErrors()` and `reset()`. The root dispatches `fluid-primitives:field:valuechange` once a value has settled, 100 ms after the last keystroke by default.

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Field",
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
