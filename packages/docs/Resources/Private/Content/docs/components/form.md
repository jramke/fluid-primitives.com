# Form

**A powerful form component with client-side validation, AJAX submission, and seamless Extbase integration.**

{% component: "ui:referenceButtons", arguments: { "name": "Form", "skipZag": true } %}

{% component: "ui:componentExample", arguments: { "componentName": "FormExample", "additionalFiles": {"FormExample.ts": "EXT:docs/Resources/Private/Components/FormExample/FormExample.entry.ts"} } %}

## Features

- Client-side validation powered by TanStack Form (Standard Schema / Zod or validator functions)
- AJAX form submission with automatic error handling
- Seamless Extbase controller integration
- Form and field level validation (change, blur, submit, and async)
- Form state management (submitting, dirty, touched, invalid, success, error)
- Works with all Field-aware components and basic HTML inputs
- Automatic field name prefixing for Extbase

## Installation

{% component: "ui:installationSection", arguments: { "name": "Form" } %}

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Form",
        "skipZag": true,
        "parts": [
            ["", "Submits and manages the form state. Renders a `<form>` element."]
        ]
    }
%}

## Anatomy

```html
<primitives:form>
    <primitives:field.root>
        <primitives:field.label />
        <primitives:field.control asChild="{true}">
            <!-- Your form input here -->
        </primitives:field.control>
        <primitives:field.error />
    </primitives:field.root>
</primitives:form>
```
