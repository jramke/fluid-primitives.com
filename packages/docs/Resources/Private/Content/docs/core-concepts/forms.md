# Forms

A guide to building forms with Fluid Primitives — covering AJAX submission, Extbase integration, client-side validation, and field state management.

## Overview

The Form component replaces TYPO3's `f:form` ViewHelper with an AJAX-first alternative. Instead of a full-page reload, it submits via `fetch`, handles server-side Extbase validation errors, and updates field state without reloading the page.

**What it gives you:**

- AJAX submission — no full-page reload
- Automatic Extbase field name prefixing (`tx_myext[MyObject][field]`)
- 422 error mapping from Extbase validation to individual fields
- Optional client-side pre-validation with Zod
- Form state (`ready`, `submitting`, `invalid`, `success`, `error`) exposed as `data-state` for CSS
- Field-level error display, label association, and ARIA wiring via the Field component
- Works with all Field-aware primitives: Select, Checkbox, RadioGroup, NumberInput, and plain HTML inputs

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

## Client-Side Validation with Zod

Install Zod separately:

```bash
npm install zod
```

Pass a Zod schema to the `Form` constructor. Validation runs on blur and before submission. Errors are mapped to fields by key:

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
    schema,
    onSubmit: async ({ formData, api, post }) => {
        const response = await post(api.getAction(), formData);
        return response.ok;
    },
});
```

Schema keys must match the field `name` props in the template. If client-side validation fails, the form stays in the `invalid` state and focus moves to the first invalid field. The `onSubmit` callback is not called.

## Manual Client-Side Validation

If you prefer not to use Zod, throw a `ValidationError` from `onSubmit`:

```typescript
import { Form, ValidationError } from 'fluid-primitives/form';

const form = new Form({
    ...data.props,
    onSubmit: async ({ formData, api }) => {
        const email = formData.get('email') as string;

        if (!email || !email.includes('@')) {
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

`ValidationError` accepts a `Record<string, { messages: string[] }>` with field names as keys. Throwing it transitions the form to `invalid` and maps errors to fields exactly like a 422 server response.

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
