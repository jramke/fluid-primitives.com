# Form

**A powerful form component with client-side validation, AJAX submission, and seamless Extbase integration.**

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

<!-- ## Usage

### Fluid Template

```html
<primitives:form action="submit" objectName="myForm" class="space-y-4">
    <primitives:field.root name="email">
        <primitives:field.label>Email</primitives:field.label>
        <primitives:field.control asChild="{true}">
            <input type="email" class="input" />
        </primitives:field.control>
        <primitives:field.error />
    </primitives:field.root>

    <primitives:field.root name="message">
        <primitives:field.label>Message</primitives:field.label>
        <primitives:field.control asChild="{true}">
            <textarea class="input" rows="4"></textarea>
        </primitives:field.control>
        <primitives:field.error />
    </primitives:field.root>

    <button type="submit">Submit</button>
</primitives:form>
```

### Client-Side (JavaScript)

```typescript
import { getHydrationData } from 'fluid-primitives';
import { Form } from 'fluid-primitives/form';
import * as v from 'valibot';

const data = getHydrationData('form', 'my-form-id');
if (!data) return;

const form = new Form({
    ...data.props,
    schema: v.object({
        email: v.pipe(v.string(), v.email('Please enter a valid email')),
        message: v.pipe(v.string(), v.minLength(10, 'Message must be at least 10 characters')),
    }),
    onSubmit: async ({ formData, api, post }) => {
        const response = await post(api.getAction(), formData);
        return response.ok;
    },
});
form.init();
```

## Examples

### Basic Form with Validation

```html
<primitives:form action="contact" objectName="contact" rootId="contact-form">
    <primitives:field.root name="name" required="{true}">
        <primitives:field.label>Name</primitives:field.label>
        <primitives:field.control asChild="{true}">
            <input type="text" class="input" />
        </primitives:field.control>
        <primitives:field.error />
    </primitives:field.root>

    <button type="submit">Send</button>
</primitives:form>
```

```typescript
import { getHydrationData } from 'fluid-primitives';
import { Form } from 'fluid-primitives/form';
import * as v from 'valibot';

const data = getHydrationData('form', 'contact-form');
if (!data) return;

const form = new Form({
    ...data.props,
    schema: v.object({
        name: v.pipe(v.string(), v.minLength(1, 'Name is required')),
    }),
    onSubmit: async ({ formData, api, post }) => {
        const response = await post(api.getAction(), formData);
        return response.ok;
    },
});
form.init();
```

### With Reactive Fields

Enable live updates for specific fields during form interactions.

```typescript
const form = new Form({
    ...data.props,
    schema: v.object({
        password: v.pipe(v.string(), v.minLength(8, 'Password must be at least 8 characters')),
        confirmPassword: v.string(),
    }),
    reactiveFields: ['password', 'confirmPassword'],
    onSubmit: async ({ formData, api, post }) => {
        const response = await post(api.getAction(), formData);
        return response.ok;
    },
});
```

### Custom Render Function

Access form state in a custom render function for advanced UI updates.

```typescript
const form = new Form({
    ...data.props,
    render: form => {
        const submitButton = form.api.getFormEl()?.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = form.api.isSubmitting;
            submitButton.textContent = form.api.isSubmitting ? 'Submitting...' : 'Submit';
        }
    },
});
```

### Server-Side Validation with Extbase

The Form component works seamlessly with Extbase controllers and validators.

**Controller:**

```php
<?php

namespace MyVendor\MyExt\Controller;

use MyVendor\MyExt\Domain\Model\DTO\ContactDTO;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class ContactController extends ActionController
{
    public function formAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }

    public function submitAction(ContactDTO $contact): ResponseInterface
    {
        // Process the form data
        return $this->jsonResponse(json_encode(['success' => true]));
    }

    protected function errorAction(): ResponseInterface
    {
        $validationErrors = $this->arguments->validate()->getFlattenedErrors();
        $errorMessages = [];

        foreach ($validationErrors as $property => $errors) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getMessage();
            }
            $errorMessages[$property] = $messages;
        }

        if (!empty($errorMessages)) {
            $response = $this->jsonResponse(json_encode($errorMessages))->withStatus(422);
            throw new PropagateResponseException($response, 422);
        }

        return parent::errorAction();
    }
}
```

**DTO with Validators:**

```php
<?php

namespace MyVendor\MyExt\Domain\Model\DTO;

use TYPO3\CMS\Extbase\Annotation\Validate;

class ContactDTO
{
    #[Validate(['validator' => 'NotEmpty'])]
    #[Validate(['validator' => 'EmailAddress'])]
    private string $email = '';

    #[Validate(['validator' => 'NotEmpty'])]
    #[Validate([
        'validator' => 'StringLength',
        'options' => ['minimum' => 10],
    ])]
    private string $message = '';

    // getters and setters...
}
```

## Anatomy

```html
<primitives:form>
    <primitives:field.root>
        <primitives:field.label />
        <primitives:field.control asChild="{true}">
            <!-- input -->

        </primitives:field.control>
        <primitives:field.description />
        <primitives:field.error />
    </primitives:field.root>

    <button type="submit">Submit</button>

</primitives:form>

````

## API Reference

### Form (Server-Side Props)

| Prop            | Type     | Default | Description                                             |
| --------------- | -------- | ------- | ------------------------------------------------------- |
| `action`        | `string` | -       | The Extbase controller action to submit to.             |
| `controller`    | `string` | -       | The Extbase controller name.                            |
| `extensionName` | `string` | -       | The extension name.                                     |
| `pluginName`    | `string` | -       | The plugin name.                                        |
| `pageUid`       | `int`    | -       | Target page UID for the form action.                    |
| `actionUri`     | `string` | -       | Override the form action URI directly.                  |
| `objectName`    | `string` | -       | The object name for property mapping (e.g., `contact`). |
| `arguments`     | `array`  | `[]`    | Additional arguments to pass to the action.             |
| `method`        | `string` | `post`  | HTTP method (get or post).                              |

### Form (Client-Side Props)

| Prop             | Type            | Default | Description                                      |
| ---------------- | --------------- | ------- | ------------------------------------------------ |
| `schema`         | `ValibotSchema` | -       | Valibot validation schema.                       |
| `reactiveFields` | `string[]`      | `[]`    | Fields that trigger live re-validation on input. |
| `onSubmit`       | `function`      | -       | Async function called on form submission.        |
| `render`         | `function`      | -       | Custom render function for UI updates.           |

### Form API

| Property/Method | Type              | Description                               |
| --------------- | ----------------- | ----------------------------------------- |
| `isSubmitting`  | `boolean`         | Whether the form is currently submitting. |
| `isDirty`       | `boolean`         | Whether any field has been modified.      |
| `isInvalid`     | `boolean`         | Whether the form has validation errors.   |
| `isSuccessful`  | `boolean`         | Whether the form submission succeeded.    |
| `isError`       | `boolean`         | Whether the form submission failed.       |
| `getValues()`   | `FormData`        | Returns current form values.              |
| `getErrors()`   | `object`          | Returns validation errors by field name.  |
| `getDirty()`    | `object`          | Returns dirty state by field name.        |
| `getTouched()`  | `object`          | Returns touched state by field name.      |
| `getFormEl()`   | `HTMLFormElement` | Returns the form DOM element.             |
| `getFields()`   | `Map`             | Returns all registered field machines.    |
| `getAction()`   | `string`          | Returns the form action URL.              |

## Form States

The form component has the following states:

- **ready** - Initial state, form is ready for input
- **submitting** - Form is being submitted
- **invalid** - Validation failed (client or server)
- **success** - Form submission succeeded
- **error** - Form submission failed

Access these via `data-state` attribute or `api.isSubmitting`, etc.

## Validation

### Client-Side with Valibot

```typescript
import * as v from 'valibot';

const schema = v.object({
    email: v.pipe(v.string(), v.email('Invalid email address')),
    age: v.pipe(v.number(), v.minValue(18, 'Must be 18 or older')),
    website: v.optional(v.pipe(v.string(), v.url())),
});
````

### Validation Timing

- **On blur**: Fields are validated when they lose focus
- **On input**: Fields with errors are re-validated as the user types
- **On submit**: All fields are validated before submission
- **Reactive fields**: Fields in `reactiveFields` array get immediate re-validation

## Data Attributes

The form element exposes these data attributes:

- `data-state` - Current form state (ready, submitting, invalid, success, error)
- `data-submitting` - Present when submitting
- `data-invalid` - Present when form has errors
- `data-dirty` - Present when any field has been modified

## Styling Examples

````css
/* Disable submit button while submitting */
form[data-submitting] button[type='submit'] {
    opacity: 0.5;
    pointer-events: none;
}

/* Show loading indicator */
form[data-submitting]::after {
    content: '';
    /* spinner styles */
}

/* Style invalid form */
form[data-invalid] {
    border-color: var(--color-destructive);
}
``` -->
````
