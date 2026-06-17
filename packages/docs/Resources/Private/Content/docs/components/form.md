# Form

**A powerful form component with client-side validation, AJAX submission, and seamless Extbase integration.**

{% component: "ui:referenceButtons", arguments: { "name": "Form", "skipZag": true } %}

{% component: "ui:componentExample", arguments: { "componentName": "FormExample", "additionalFiles": {"FormExample.ts": "EXT:docs/Resources/Private/Components/FormExample/FormExample.entry.ts"} } %}

## Features

- Optional client-side validation with Standard Schema-compatible validators or custom callbacks
- AJAX form submission with automatic error handling
- Seamless Extbase controller integration
- Real-time field validation on blur and input
- Form state management (submitting, dirty, invalid, success, error)
- Built-in primitives for editable content, state indicators, and status text
- Works with all Field-aware components and basic HTML inputs
- Automatic field name prefixing for Extbase

See the complete [Form Guide](/docs/core-concepts/forms) for a complete form integration.

## Installation

{% component: "ui:installationSection", arguments: { "name": "Form" } %}

Client-side validation is configured in your entry file with the `validation` option, not in the Fluid template. Pass either a Standard Schema-compatible validator such as Zod or a synchronous callback that reads `formData` directly. For submit results, return `true`, `false`, or field errors from `onSubmit`, and use `api.setErrorText()` or `api.setSuccessText()` for form-level messages.

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Form",
        "skipZag": true,
        "parts": [
            ["", "Submits and manages the form state. Renders a `<form>` element."],
            ["content", "Wraps the editable form UI. It stays visible in `ready`, `invalid`, and `submitting`, and hides in `error` and `success`. Renders a `<div>` element."],
            ["indicator", "Displays content for an exact form state such as `error`, `success`, or `submitting`. Renders a `<div>` element."],
            ["errorText", "Displays the current form-level error text set through the Form API, or its slotted fallback text. Renders a `<span>` element."],
            ["successText", "Displays the current form-level success text set through the Form API, or its slotted fallback text. Renders a `<span>` element."]
        ]
    }
%}

## Anatomy

```html
<primitives:form>
    <primitives:form.content>
        <primitives:field.root>
            <primitives:field.label />
            <primitives:field.control asChild="{true}">
                <!-- Your form input here -->
            </primitives:field.control>
            <primitives:field.error />
        </primitives:field.root>
    </primitives:form.content>

    <primitives:form.indicator state="{f:constant(name: 'Jramke\FluidPrimitives\Enum\FormState::Error')}">
        <primitives:form.errorText />
    </primitives:form.indicator>
</primitives:form>
```
