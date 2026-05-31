# Forms

A guide to building forms with Fluid Primitives — covering AJAX submission, Extbase integration, client-side validation, and field state management.

## Overview

The Form component replaces TYPO3's `f:form` ViewHelper with an AJAX-first alternative. Instead of a full-page reload, it submits via `fetch`, handles server-side Extbase validation errors, and updates field state without reloading the page.

**What it gives you:**

- AJAX submission — no full-page reload
- Automatic Extbase field name prefixing (`tx_myext[MyObject][field]`)
- 422 error mapping from Extbase validation to individual fields
- Optional client-side validation powered by TanStack Form (Standard Schema / Zod or validator functions)
- Form state (`ready`, `submitting`, `invalid`, `success`, `error`) exposed as `data-state` for CSS
- Field-level error display, label association, and ARIA wiring via the Field component
- Works with all Field-aware primitives: Select, Checkbox, RadioGroup, NumberInput, and plain HTML inputs

## How values flow

The form has two value worlds:

| Layer                               | Shape                                                                                      | Used for                        |
| ----------------------------------- | ------------------------------------------------------------------------------------------ | ------------------------------- |
| TanStack form (`form.state.values`) | Native JS values: `string`, `number`, `boolean`, `string[]`, `File`, `File[]`, `Date`, ... | Client-side validation, schemas |
| Submission (`FormData`)             | Native HTML form-submission bytes (`'1'` / omitted / multipart for files)                  | Sending to the server (TYPO3)   |

Each primitive (NumberInput, Select, Checkbox, RadioGroup, ...) pushes its own JS-typed value into the bound TanStack field. A serializer converts those values to `FormData` only at submit time.

**Conversion rules**

- `boolean true` → `'1'` (matches Extbase Boolean validators)
- `boolean false`, `null`, `undefined` → omitted (matches an unchecked checkbox in a real `<form>`)
- `File` / `Blob` → appended as a multipart part
- `File[]` / `string[]` → multiple appends with `name[]`
- `Date` → ISO string
- `number` → `String(n)`
- everything else → `String(value)`

This separation is why your validation schemas can stay idiomatic:

```typescript
// Use JS types in your schema
z.object({
    name: z.string().min(1),
    ticketCount: z.number().min(1).max(10),
    privacy: z.literal(true), // not z.literal('1')
    a11yNeeds: z.array(z.string()),
    avatar: z.instanceof(File), // future file upload primitive
});
```

…while the server still receives plain form-encoded bytes that TYPO3's property mapper understands (`tx_myext[obj][privacy]=1`, file uploads via `$request->getUploadedFiles()`, etc.).

## Installation

First you should add the Form and Field primitives to your project:

```bash
typo3 ui:add form && typo3 ui:add field
```

## Basic Setup

### Template

Use `ui:form` with `action` pointing to your Extbase action and `objectName` matching the argument name in your controller:

```html
<ui:form action="registration" objectName="eventRegistration" object="{eventRegistration}" controlled="{true}" rootId="registration-form">
    <ui:field.root name="email" required="{true}">
        <ui:field.label>Email</ui:field.label>
        <ui:field.control asChild="{true}">
            <ui:input type="email" autocomplete="email" />
        </ui:field.control>
        <ui:field.description>Used for your confirmation email.</ui:field.description>
        <ui:field.error />
    </ui:field.root>

    <ui:button type="submit">Register</ui:button>
</ui:form>
```

The form renders as a standard `<form>` with `novalidate` and the Extbase action URL resolved server-side. The `object` prop pre-populates field values from an existing model instance.

### Controller

```php
<?php

declare(strict_types=1);

namespace Vendor\MyExtension\Controller;

use Vendor\MyExtension\Domain\Model\EventRegistration;
use Jramke\FluidPrimitives\Traits\AjaxValidationTrait;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

final class EventRegistrationController extends ActionController
{
    use AjaxValidationTrait;

    public function registrationAction(EventRegistration $eventRegistration): ResponseInterface
    {
        // Process the submission
        // $this->eventRegistrationRepository->save($eventRegistration);

        return $this->jsonResponse(json_encode(['success' => true]))->withStatus(200);
    }

    protected function errorAction(): ResponseInterface
    {
        // Converts Extbase validation errors to a 422 JSON response
        $this->throwJsonValidationErrorResponse();
        return parent::errorAction();
    }
}
```

The `AjaxValidationTrait` provides `throwJsonValidationErrorResponse()`, which intercepts Extbase's normal `errorAction` redirect and instead returns a 422 JSON response with field-keyed error messages. The Form component reads this response and assigns errors to individual fields.

{% component: "ui:alert", arguments: {"title": "Pro Tip", "text": "If your controller action is not registered as a standalone Plugin you can use `throw new PropagateResponseException` to return a plain json response.", "variant": "info"} %}

### Entry File (TypeScript)

The form requires a client-side entry file. Use `controlled="{true}"` on the root and fetch its hydration data by ID:

```typescript
import { getHydrationData, mount } from 'fluid-primitives';
import { Form } from 'fluid-primitives/form';

mount('my-form', () => {
    const data = getHydrationData('form', 'registration-form');
    if (!data) return;

    const form = new Form({
        ...data.props,
        onSubmit: async ({ formData, api, post }) => {
            const response = await post(api.getAction(), formData);
            return response.ok;
        },
    });

    form.init();
});
```

The `post()` helper automatically adds the Extbase field name prefix (`tx_myext[MyObject][field]`) before sending, so your form fields can use plain names like `email` in the template.

## The Field Component

`ui:field.root` wraps any input and wires up labels, errors, descriptions, and ARIA attributes. The `name` prop is required and must match the property name on your model.

### Anatomy

```html
<ui:field.root name="email" required="{true}">
    <ui:field.label>Email address</ui:field.label>
    <ui:field.control asChild="{true}">
        <!-- native input or primitive goes here -->
        <ui:input type="email" />
    </ui:field.control>
    <ui:field.description>We'll send your confirmation here.</ui:field.description>
    <ui:field.error />
</ui:field.root>
```

**Parts:**

- `field.label` — renders a `<label>` with `for` pointing to the control
- `field.control` — when `asChild="{true}"`, spreads the field's ARIA attributes onto the child element
- `field.description` — optional helper text, wired to `aria-describedby`
- `field.error` — renders the error message, wired to `aria-describedby` and only shown when the field is in an error state

### Field Props

`ui:field.root` accepts these props:

- `name` (`string`, required) — maps to the model property and form field name
- `required` (`boolean`) — marks the field required; propagates to the control
- `disabled` (`boolean`) — disables the field and all contained controls
- `readOnly` (`boolean`) — sets the field and controls to read-only
- `invalid` (`boolean`) — forces the field into an invalid state (e.g. pre-populated server error)
- `defaultValue` (`mixed`) — pre-populates the field value

### Inherited Field Props on Primitives

When a Field-aware primitive is placed inside a `ui:field.root`, the field's state automatically propagates into the primitive. You do not need to repeat `disabled`, `required`, etc. on the primitive itself.

```html
<!-- disabled on field.root propagates to the Select automatically -->
<ui:field.root name="country" disabled="{true}">
    <ui:select.root collection="{countries}">
        <ui:select.label>Country</ui:select.label>
        <ui:select.control>
            <ui:select.trigger placeholder="Pick a country" />
        </ui:select.control>
        <ui:select.content>
            <f:for each="{countries.items}" as="item">
                <ui:select.item item="{item}">
                    <ui:select.itemText>{item.label}</ui:select.itemText>
                </ui:select.item>
            </f:for>
        </ui:select.content>
    </ui:select.root>
    <ui:field.error />
</ui:field.root>
```

## Server-Side Validation

### Extbase Model Validation

Use PHP 8 attributes on your model to declare validation rules. Extbase runs these before your action is called. If validation fails, `errorAction` is triggered — which the `AjaxValidationTrait` converts to a 422 JSON response.

```php
<?php

declare(strict_types=1);

namespace Vendor\MyExtension\Domain\Model;

use TYPO3\CMS\Extbase\Annotation\Validate;

class EventRegistration
{
    #[Validate(['validator' => 'NotEmpty'])]
    #[Validate(['validator' => 'EmailAddress'])]
    #[Validate(['validator' => 'StringLength', 'options' => ['maximum' => 255]])]
    private string $email = '';

    #[Validate(['validator' => 'NotEmpty'])]
    #[Validate(['validator' => 'StringLength', 'options' => ['maximum' => 255]])]
    private string $name = '';

    #[Validate(['validator' => 'Boolean', 'options' => ['is' => true]])]
    private bool $privacy = false;

    // getters and setters...
}
```

The 422 JSON response has the shape:

```json
{
    "eventRegistration.email": ["This field must be a valid email address."],
    "eventRegistration.name": ["This field must not be empty."]
}
```

The Form component maps each key to the corresponding field by name and displays the error in `ui:field.error`.

### Manual 422 Response

For business-rule validation that doesn't belong in the model, return a 422 directly from your action:

```php
public function registrationAction(EventRegistration $eventRegistration): ResponseInterface
{
    if ($eventRegistration->getTicketType() === 'vip') {
        $payload = ['eventRegistration.ticketType' => ['VIP tickets are sold out.']];
        return $this->jsonResponse(json_encode($payload))->withStatus(422);
    }

    // continue with save...
}
```

### Generic Error State

When the server returns a non-422 error (500, etc.), the form transitions to `data-state="error"`. Use this to show a fallback message:

```html
<ui:form ... class="group">

    <!-- Main form fields, hidden on error or success -->
    <div class="group-[[data-state=error]]:hidden group-[[data-state=success]]:hidden">
        <!-- fields -->
    </div>

    <!-- Error state -->
    <div class="hidden group-[[data-state=error]]:block">
        <p {ui:ref(name: 'error-message')}>Something went wrong. Please try again.</p>
        <ui:button type="reset">Back to form</ui:button>
    </div>

    <!-- Success state -->
    <div class="hidden group-[[data-state=success]]:block">
        <p>Your registration was submitted successfully. Thank you!</p>
    </div>

</ui:form>
```

Add `group` to the form's `class` so Tailwind's group variant selectors work against `data-state`.

In your entry file, update the error message element from the response body:

```typescript
onSubmit: async ({ formData, api, post }) => {
    const response = await post(api.getAction(), formData);
    const json = await response.json();

    if (!response.ok) {
        const errorMessageEl = hydrator.getElement('error-message');
        if (errorMessageEl) {
            errorMessageEl.textContent = json.message ?? 'Something went wrong.';
        }
    }

    return response.ok;
},
```

## Client-Side Validation

Client-side validation is powered by [TanStack Form](https://tanstack.com/form). The `Form` machine wraps a TanStack `FormApi`, so you configure validation with TanStack's `validators` option — at the form level here, or per field on `ui:field.root` (see [Field-Level Validation](#field-level-validation)).

Validators run on the events you wire them to (`onChange`, `onBlur`, `onSubmit`, plus their async counterparts). Errors are mapped to fields by key and exposed through `ui:field.error`.

### With a Standard Schema (Zod)

Any [Standard Schema](https://github.com/standard-schema/standard-schema) library works. Zod v4 schemas are Standard Schema compliant and can be passed directly to a validator:

```bash
npm install zod
```

```typescript
import { z } from 'zod';
import { Form } from 'fluid-primitives/form';

const schema = z.object({
    name: z.string().min(1, 'Please enter your name'),
    email: z.email('Please enter a valid email address'),
    ticketCount: z.coerce.number().min(1).max(10),
    privacy: z.literal('1', 'You must accept the privacy policy'),
});

const form = new Form({
    ...data.props,
    validators: {
        onChange: schema,
        onSubmit: schema,
    },
    onSubmit: async ({ formData, api, post }) => {
        const response = await post(api.getAction(), formData);
        return response.ok;
    },
});
```

Schema keys must match the field `name` props in the template. If validation fails on submit, the form stays in the `invalid` state and focus moves to the first invalid field. The `onSubmit` callback is not called.

### With a Validator Function

For logic that doesn't fit a schema, pass a function. Return an error keyed by field name (or a single string for a form-level error):

```typescript
const form = new Form({
    ...data.props,
    validators: {
        onChange: ({ value }) => {
            const errors: Record<string, string> = {};
            if (!value.email?.includes('@')) {
                errors.email = 'Please enter a valid email address';
            }
            return Object.keys(errors).length ? { fields: errors } : undefined;
        },
    },
    onSubmit: async ({ formData, api, post }) => {
        const response = await post(api.getAction(), formData);
        return response.ok;
    },
});
```

### Field-Level Validation

Validators can also live on the individual field via the `validators` prop on `ui:field.root`. This keeps a field's rules colocated with the field and is useful for async/remote checks:

```html
<ui:field.root name="username" validators="{...}">
    <!-- ... -->
</ui:field.root>
```

```typescript
const field = new Field({
    ...data.props,
    validators: {
        onChange: ({ value }) => (value.length < 3 ? 'Must be at least 3 characters' : undefined),
        onChangeAsyncDebounceMs: 500,
        onChangeAsync: async ({ value }) => {
            const res = await fetch(`/api/check-username?u=${value}`);
            return (await res.json()).taken ? 'Username is taken' : undefined;
        },
    },
});
```

## Server-Side Errors from `onSubmit`

To surface server-side (or business-rule) errors on individual fields, throw a `ValidationError` from `onSubmit`. The form writes the messages into the matching fields and transitions to `invalid`:

```typescript
import { Form, ValidationError } from 'fluid-primitives/form';

const form = new Form({
    ...data.props,
    onSubmit: async ({ value, formData, api }) => {
        if (!value.email || !String(value.email).includes('@')) {
            throw new ValidationError({
                email: { messages: ['Please enter a valid email address.'] },
            });
        }

        const response = await fetch(api.getAction(), {
            method: 'POST',
            body: formData,
        });

        return response.ok;
    },
});
```

`ValidationError` accepts a `Record<string, { messages: string[] }>` with field names as keys. The `post()` helper throws it automatically when the server returns a 422 response, so the same mechanism handles both Extbase validation and manual checks.

## Form State

The form element receives a `data-state` attribute that reflects the current state:

- `ready` — initial state, form is ready for input
- `submitting` — submission in progress
- `invalid` — validation failed (client or server)
- `success` — `onSubmit` returned `true`
- `error` — `onSubmit` returned `false` or threw a non-validation error

Use `data-state` in CSS to conditionally show/hide sections or style the submit button:

```css
form[data-submitting] button[type='submit'] {
    opacity: 0.5;
    pointer-events: none;
}
```

The `render` callback on the `Form` constructor runs every time the form state changes. Use it to update UI elements that are outside the form machine's automatic wiring:

```typescript
render: form => {
    const submitButton = hydrator.getElement('submit-button');
    if (submitButton) {
        submitButton.setAttribute('aria-disabled', form.api.isSubmitting ? 'true' : 'false');
        submitButton.textContent = form.api.isSubmitting ? 'Submitting...' : 'Register';
    }
},
```

## Complete Example: Event Registration

A full event registration form with Extbase model validation, a server-side business rule (VIP tickets sold out), and Zod client-side pre-validation.

{% component: "ui:componentExample", arguments: { "componentName": "EventRegistration", "additionalFiles": {"EventRegistration.entry.ts": "EXT:docs/Resources/Private/Components/EventRegistration/EventRegistration.entry.ts", "EventRegistrationController.php": "EXT:docs/Classes/Controller/EventRegistrationController.php", "EventRegistration.php": "EXT:docs/Classes/Domain/Model/EventRegistration.php"} } %}
