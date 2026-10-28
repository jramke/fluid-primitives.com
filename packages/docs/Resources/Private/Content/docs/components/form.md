# Form

**A form that submits to an Extbase action with `fetch` and shows validation errors on its fields.**

{% component: "ui:referenceButtons", arguments: { "name": "Form" } %}

{% component: "ui:componentExample", arguments: { "componentName": "FormExample", "additionalFiles": {"FormExample.ts": "EXT:docs/Resources/Private/Components/FormExample/FormExample.entry.ts"} } %}

## Features

- Submits with `fetch` and maps the `422` validation response of an Extbase action onto the fields
- Renders Extbase's hidden `__trustedProperties` and `__identity` fields and sends the values under the plugin's argument namespace
- Client-side validation with a Standard Schema validator such as Zod, or with a callback
- Errors from native constraints, your schema or callback, async checks of single fields and the server show on the field according to its validation mode
- Form state as `data-state` (`ready`, `submitting`, `invalid`, `success`, `error`), plus `data-submitting`, `data-invalid`, `data-dirty` and `data-touched`
- Parts for the editable content, state indicators and status text
- Works with every Field-aware primitive and plain HTML inputs

The [Forms guide](/docs/core-concepts/forms) walks through a form from the template to the controller.

## Anatomy

```html
<primitives:form.root>
    <primitives:form.content>
        <primitives:field.root>
            <primitives:field.label />
            <primitives:field.control asChild="{true}">
                <!-- Your form input here -->
            </primitives:field.control>
            <primitives:field.errorText />
        </primitives:field.root>
    </primitives:form.content>

    <primitives:form.indicator
        state="{f:constant(name: 'Jramke\FluidPrimitives\Enum\FormState::Error')}"
    >
        <primitives:form.errorText />
    </primitives:form.indicator>
</primitives:form.root>
```

## Installation

{% component: "ui:installationSection", arguments: { "name": "Form" } %}

Options that take a function go to `Form` in your entry file, because a function cannot travel through the server-rendered markup. Mount the form from your entry file as shown in [Send the values](/docs/core-concepts/forms#content-send-the-values).

- `validation` - a Standard Schema validator such as Zod, or a synchronous callback that returns errors keyed by field name, see [Client-Side Validation](/docs/core-concepts/forms#content-client-side-validation)
- `onSubmit` - receives `values` and `api`, and returns `true`, `false` or field errors. Send the values with [`extbase.post()`](/docs/client-utilities/extbase), set the form-level messages with `api.setErrorText()` and `api.setSuccessText()`
- `render` - runs whenever the form state changes, for UI outside the form's own wiring

`values` uses your field names as dot paths such as `person.name` and keeps leaf values as `string | File`. Validation and server errors are keyed by field name too, so a nested field reports under `person.name`.

### Choose when fields validate

Set `validationMode` on the form to give all its fields the same timing, and `inputDebounceMs` to change how long they wait for a pause in typing before they validate: 100 by default, at least 50. A field that sets its own `validationMode` keeps it. The modes are described under [Validation mode](/docs/components/field#content-validation-mode) of the field.

```html
<primitives:form.root
    validationMode="{f:constant(name: 'Jramke\FluidPrimitives\Enum\ValidationMode::OnBlur')}"
    inputDebounceMs="300"
></primitives:form.root>
```

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Form",
        "parts": [
            ["root", "Submits and manages the form state. Renders a `<form>` element."],
            ["content", "Wraps the editable form UI. It stays visible in `ready`, `invalid`, and `submitting`, and hides in `error` and `success`. Renders a `<div>` element."],
            ["indicator", "Displays content for an exact form state such as `error`, `success`, or `submitting`. Renders a `<div>` element."],
            ["errorText", "Displays the current form-level error text set through the Form API, or its slotted fallback text. Renders a `<span>` element."],
            ["successText", "Displays the current form-level success text set through the Form API, or its slotted fallback text. Renders a `<span>` element."]
        ]
    }
%}
