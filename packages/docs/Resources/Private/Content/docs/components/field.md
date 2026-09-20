# Field

**A form field wrapper that provides accessible labeling, error handling, and state management for form inputs.**

{% component: "ui:referenceButtons", arguments: { "name": "Field", "skipZag": true } %}

{% component: "ui:componentExample", arguments: { "componentName": "Field.examples.simple", "withEntryFile": true } %}

## Features

- Automatic label association with form controls
- Error message display with proper ARIA attributes
- Description text support for additional context
- State management for disabled, required, readonly, and invalid states
- Seamless integration with Form component for validation
- Works with all form-related primitives (Checkbox, Select, RadioGroup, etc.) and native inputs

## Installation

{% component: "ui:installationSection", arguments: { "name": "Field" } %}

## Examples

### Required Field

Mark a field as required.

{% component: "ui:componentExample", arguments: { "componentName": "Field.examples.required" } %}

### With Description

Add helpful description text below the input.

{% component: "ui:componentExample", arguments: { "componentName": "Field.examples.withDescription" } %}

### Invalid State

Indicate that the field has an error and display an error message.

{% component: "ui:componentExample", arguments: { "componentName": "Field.examples.invalid" } %}

### Disabled Field

Disable the entire field.

{% component: "ui:componentExample", arguments: { "componentName": "Field.examples.disabled" } %}

### With Checkbox

Use with the Checkbox component.

{% component: "ui:componentExample", arguments: { "componentName": "Field.examples.withCheckbox" } %}

### Linked Fields

A field can react to a _different_ field's value changing via the `listenTo` prop - names of sibling fields within the same form to watch. Two independent things happen, on two independent triggers: a `fluid-primitives:field:dependencychange` event is dispatched on its root element on _every_ value change of a listened-to field, for any imperative DOM reaction you need; and this field's own validation re-runs only when a listened-to field _validates itself_ - on blur, or on change once it already has an error - the same conditions that field already uses to revalidate itself, not on every keystroke.

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

The classic case is a password confirmation field - `passwordConfirm` listens to `password`, so blurring `password` after editing it (or editing it again once it's already shown an error) re-validates `passwordConfirm` too, instead of leaving a stale result:

```html
<ui:field.root name="password">
    <ui:input.root type="password">
        <ui:input.label>Password</ui:input.label>
        <ui:input.input />
    </ui:input.root>
    <ui:field.error />
</ui:field.root>

<ui:field.root name="passwordConfirm" listenTo="{0: 'password'}">
    <ui:input.root type="password">
        <ui:input.label>Confirm password</ui:input.label>
        <ui:input.input />
    </ui:input.root>
    <ui:field.error />
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

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Field",
        "skipZag": true,
        "parts": [
            ["root", "Provides shared field state for labels, descriptions, errors, and controls. Renders a `<div>` element."],
            ["label", "Labels the associated form control. Renders a `<label>` element."],
            ["control", "Wraps the slotted form control and wires up shared field attributes. Renders the element defined by the `asChild` prop."],
            ["description", "Displays help or supporting text for the field. Renders a `<div>` element."],
            ["error", "Displays validation error messages for the field. Renders a `<div>` element."]
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
    <primitives:field.description />
    <primitives:field.error />
</primitives:field.root>
```
