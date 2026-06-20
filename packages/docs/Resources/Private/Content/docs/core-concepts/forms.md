# Forms

A guide to building forms with Fluid Primitives — covering AJAX submission, Extbase integration, client-side validation, and field state management.

## Overview

The Form component replaces TYPO3's `f:form` ViewHelper with an AJAX-first alternative. Instead of a full-page reload, it submits via `fetch`, handles server-side Extbase validation errors, and updates field state without reloading the page.

**What it gives you:**

- AJAX submission — no full-page reload
- Automatic Extbase field name prefixing (`tx_myext[MyObject][field]`)
- 422 error mapping from Extbase validation to individual fields
- Optional client-side validation with Standard Schema-compatible validators or callbacks
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

The `post()` helper automatically adds the Extbase field name prefix (`tx_myext[MyObject][field]`) before sending, so your form fields can use plain names like `email` in the template. It should be used for endpoints that expect the same payload shape as the form itself. It also handles 422 JSON validation responses for you by mapping them back to fields and transitioning the form to `invalid`.

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

Each form should have server-side validation to ensure your controller gets the expected data. Since server-side validation cant be easily bypassed this is our main source of truth. Client-side validation "just" enhances the UX of your forms. You should use both.

Server-side errors are stored by its field and value. So like in the example, when the user submits a VIP registration he gets an error because vip tickets are sold out. When he changes the ticket type to standard the error resolves. However when the user switches back to the vip ticket we automatically show the server side error again.

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

### Submit Results And Status States

`onSubmit` can return three outcomes:

- `true` transitions the form to `success`
- `false` transitions the form to the generic `error` state
- `Record<string, { messages: string[] }>` transitions the form to `invalid` and assigns field errors

For form-level messaging, use `api.setErrorText()` and `api.setSuccessText()`. The `form.content`, `form.indicator`, `form.errorText`, and `form.successText` primitives remove the need for `group-[[data-state=...]]` selectors:

```html
<ui:form ...>
    <ui:form.content>
        <!-- fields -->
    </ui:form.content>

    <ui:form.indicator state="{f:constant(name: 'Jramke\FluidPrimitives\Enum\FormState::Error')}">
        <ui:form.errorText>Something went wrong. Please try again.</ui:form.errorText>
        <ui:button type="reset">Back to form</ui:button>
    </ui:form.indicator>

    <ui:form.indicator state="{f:constant(name: 'Jramke\FluidPrimitives\Enum\FormState::Success')}">
        <ui:form.successText>Your registration was submitted successfully. Thank you!</ui:form.successText>
    </ui:form.indicator>
</ui:form>
```

Set the status text in your entry file and return the matching submit result:

```typescript
onSubmit: async ({ formData, api, post }) => {
    const response = await post(api.getAction(), formData);
    const json = await response.json();

    if (!response.ok) {
        api.setErrorText(json.message ?? 'Something went wrong.');
        return false;
    }

    api.setSuccessText(json.message ?? 'Your registration was submitted successfully.');
    return true;
},
```

When you use `post()`, 422 JSON validation responses do not come back as a normal `Response`. They are intercepted by the Form primitive, mapped to field errors automatically, and transition the form to `invalid`.

## Client-Side Validation

To enhance the UX of your forms you should also use (slimmed down) client-side validation in addition to (more complex) server-side validation.

Client-side validation runs on blur for dirty fields and before submission. Once a field already has an error, we validate it on change too so the user gets immediate feedback while fixing it.

Each field also tracks local interaction metadata. `field.meta.isTouched` becomes `true` after the first change or blur, `field.meta.isDirty` stays `true` once the value was changed, `field.meta.isPristine` is the inverse of `isDirty`, `field.meta.isBlurred` becomes `true` after the first blur, and `field.meta.isDefaultValue` reflects whether the current value matches the initial value. The same state is mirrored to `field.root` as `data-touched`, `data-dirty`, `data-pristine`, `data-blurred`, and `data-default-value` attributes for styling.

Install your validator separately. For Zod:

```bash
npm install zod
```

You do not need an extra Standard Schema package in your app code. Just pass your existing schema object to `validation`.

Pass a synchronous Standard Schema-compatible validator to the `Form` constructor. Zod works out of the box. Errors are mapped to fields by key:

```typescript
import { z } from 'zod';
import { Form } from 'fluid-primitives/form';

const validation = z.object({
    name: z.string().min(1, 'Please enter your name'),
    email: z.email('Please enter a valid email address'),
    ticketCount: z.coerce.number().min(1).max(10),
    privacy: z.literal('1', 'You must accept the privacy policy'),
});

const form = new Form({
    ...data.props,
    validation,
    onSubmit: async ({ formData, api, post }) => {
        const response = await post(api.getAction(), formData);
        return response.ok;
    },
});
```

Any validator that implements the Standard Schema interface can be passed here without an adapter.

Validation keys must match the field `name` props in the template. If client-side validation fails, the form stays in the `invalid` state and focus moves to the first invalid field. The `onSubmit` callback is not called.

## Manual Client-Side Validation

If you prefer not to use a schema library, pass a synchronous `validation` callback. It receives the current `formData` and should return the same field-keyed error shape used by server validation responses. Missing `value` properties are filled automatically with the current field value:

```typescript
import { Form } from 'fluid-primitives/form';

const form = new Form({
    ...data.props,
    validation: ({ formData }) => {
        const errors: Record<string, { messages: string[] }> = {};
        const email = formData.get('email') as string;

        if (!email || !email.includes('@')) {
            errors.email = { messages: ['Please enter a valid email address.'] };
        }

        return errors;
    },
    onSubmit: async ({ formData, api, post }) => {
        const response = await post(api.getAction(), formData);

        return response.ok;
    },
});
```

Validation callbacks must be synchronous. Use `onSubmit` for async checks.

## Async Validation During Submission

If a validation rule depends on async work inside `onSubmit`, prefer `post()` when the endpoint accepts the same form payload shape. If that endpoint returns a non-422 response, you can still map it to field errors yourself:

```typescript
import { Form } from 'fluid-primitives/form';

const form = new Form({
    ...data.props,
    onSubmit: async ({ formData, post }) => {
        const response = await post('/email-check', formData);

        if (response.status === 409) {
            return {
                email: { messages: ['This email address is already registered.'] },
            };
        }

        if (!response.ok) {
            return false;
        }

        return true;
    },
});
```

Returning field errors from `onSubmit` transitions the form to `invalid` and maps errors to fields exactly like a 422 server response.

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

A full event registration form with Extbase model validation, a server-side business rule (VIP tickets sold out), and Zod client-side pre-validation via `validation`.

{% component: "ui:componentExample", arguments: { "componentName": "EventRegistration", "additionalFiles": {"EventRegistration.entry.ts": "EXT:docs/Resources/Private/Components/EventRegistration/EventRegistration.entry.ts", "EventRegistrationController.php": "EXT:docs/Classes/Controller/EventRegistrationController.php", "EventRegistration.php": "EXT:docs/Classes/Domain/Model/EventRegistration.php"} } %}
