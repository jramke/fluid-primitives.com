# Form

**A powerful form component with client-side validation, AJAX submission, and seamless Extbase integration.**

{% component: "ui:referenceButtons", arguments: { "name": "Form" } %}

{% component: "ui:componentExample", arguments: { "componentName": "FormExample", "additionalFiles": {"FormExample.ts": "EXT:docs/Resources/Private/Components/FormExample/FormExample.entry.ts"} } %}

## Features

- Optional client-side validation with Zod schema support
- AJAX form submission with automatic error handling
- Seamless Extbase controller integration
- Real-time field validation on blur and input
- Form state management (submitting, dirty, invalid, success, error)
- Works with all Field-aware components and basic HTML inputs
- Automatic field name prefixing for Extbase

## Installation

{% component: "ui:installationSection", arguments: { "name": "Form" } %}

## Anatomy

```html
<primitives:form.root>
    <primitives:field.root>
        <primitives:field.label />
        <primitives:field.control asChild="{true}">
            <!-- Your form input here -->
        </primitives:field.control>
        <primitives:field.error />
    </primitives:field.root>
    <primitives:form.submit />
</primitives:form.root>
```
