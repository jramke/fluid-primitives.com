# Forms

Build a form that submits to an Extbase action with `fetch`, shows server and client validation errors on its fields and reports the result without a page reload.

## Installation

Add the Form and Field primitives:

```bash
typo3 ui:add form && typo3 ui:add field
```

## Submit a form to an Extbase action

### Render the form

Point `action` at your Extbase action and set `objectName` to the name of the action's argument. `object` fills the fields from an existing model instance.

```html
<ui:form.root action="registration" objectName="eventRegistration" object="{eventRegistration}">
    <ui:field.root name="email" required="{true}">
        <ui:input.root type="email" autocomplete="email">
            <ui:input.label>Email</ui:input.label>
            <ui:input.input />
        </ui:input.root>
        <ui:field.helperText>We send your confirmation here.</ui:field.helperText>
        <ui:field.errorText />
    </ui:field.root>

    <ui:button type="submit">Register</ui:button>
</ui:form.root>
```

Each input sits in a [field](/docs/components/field). Its `name` is the property of your model (`person.name` for a nested one), which is how errors find the field. Primitives like `ui:select` or `ui:numberInput` nest the same way, and their own `label` part points the label at the right control. A native element without a primitive goes into `ui:field.control` with `asChild="{true}"`.

The values are sent under the argument namespace of the plugin the form submits to, for example `tx_myextension_eventregistration[eventRegistration][email]`. The form uses the plugin that renders it or, when you set `controller`, the plugin that registers that controller and `action`, so it can render in one plugin and post to an action of another. Set `extensionName` and `pluginName` to name the plugin yourself, they win over both. Outside an Extbase request, in a plain Fluid partial for example, pass both: with either one missing, rendering throws.

### Answer with JSON

Make sure your controller returns a valid JSON response via `$this->jsonResponse()`. To make this work you either have to register the action as an AJAX page type via Typoscript or use `throw PropagateResponseException()`.

{% component: "ui:alert", arguments: {"title": "Persistence Warning", "text": "When using `PropagateResponseException` to return a JSON response, make sure to persist your data manually before throwing the exception since we exit Extbase's normal flow.", "variant": "warning"} %}

`JsonValidationErrorsTrait` replaces the `errorAction()` of your controller. Failed validation is automatically answered with a `422` JSON response of messages keyed by field, which the form maps onto its fields. Good to know: the trait uses `PropagateResponseException` internally to return the JSON response.

```php
use Jramke\FluidPrimitives\Traits\JsonValidationErrorsTrait;
use TYPO3\CMS\Core\Http\PropagateResponseException;

final class EventRegistrationController extends ActionController
{
    use JsonValidationErrorsTrait;

    public function __construct(
        private readonly EventRegistrationRepository $eventRegistrationRepository,
    ) {}

    public function registrationAction(EventRegistration $eventRegistration): ResponseInterface
    {
        $this->eventRegistrationRepository->add($eventRegistration);

        return $this->jsonResponse(
            json_encode(['message' => 'Thank you for registering.'], JSON_THROW_ON_ERROR),
        );
    }
}
```

The trait covers every action of the controller. To keep Extbase's error handling for other actions, alias its method and dispatch by hand:

```php
final class EventRegistrationController extends ActionController
{
    use JsonValidationErrorsTrait {
        errorAction as protected jsonErrorAction;
    }

    #[\Override]
    protected function errorAction(): ResponseInterface
    {
        if ($this->actionMethodName === 'registrationAction') {
            return $this->jsonErrorAction();
        }

        return parent::errorAction();
    }
}
```

### Send the values

`ui:add form` ships an entry file that mounts every form and posts its values with [`extbase.post()`](/docs/client-utilities/extbase). The `message` of the JSON response becomes the error or success text of the form.

Write your own entry file for `validation`, `render` or a different `onSubmit`. Give the root `autoMount="{false}"` and a `rootId` such as `registration-form`, then mount it by that id (see [Hydration](/docs/core-concepts/hydration)):

```typescript
import { extbase, mount } from 'fluid-primitives';
import { Form } from 'fluid-primitives/form';

mount('ui:form', 'registration-form', ({ props }) =>
    new Form({
        ...props,
        onSubmit: async ({ api, values }) => {
            const response = await extbase.post(api.getAction(), values.toObject(), {
                prefix: api.getFieldNamePrefix(),
                objectName: api.getObjectName(),
            });

            const data: { message?: string } = await response.json().catch(() => ({}));
            if (!response.ok) {
                api.setErrorText(data.message ?? null);
                return false;
            }

            api.setSuccessText(data.message ?? null);
            return true;
        },
    }).init()
);
```

`extbase.post()` nests the values under the Extbase namespace, Extbase's hidden `__trustedProperties` and `__identity` fields included, so your fields keep plain names like `email` or `person.name`.

### Clear a property with an empty field

Every control submits its name, also while it is empty, so Extbase can clear the property it is bound to. An unchecked Checkbox or Switch, a CheckboxGroup with nothing checked, and a Select, Combobox or DatePicker without a value submit `''`. A RadioGroup with nothing chosen submits nothing, like native radios.

A field whose name ends with `[]` (`tags[]`) submits its empty value under the name without the brackets (`tags`), as Fluid's own `f:form.checkbox` does: Extbase maps `''` to an empty array or ObjectStorage. In the form values the field reads as `[]` and an unchecked box as `''`, and `extbase.post()` sends an empty list as `''` again.

A disabled control submits nothing, and neither does its empty-value input. Render your own control's input with [`ui:emptyValueInput`](/docs/viewhelpers/emptyValueInput).

## Show server-side errors on the fields

With the trait, Extbase's validation errors arrive as a `422` response. Each key is a property path, prefixed with the `objectName`:

```json
{
    "eventRegistration.email": ["Please enter a valid email address."],
    "eventRegistration.name": ["Please enter your name."]
}
```

Every key lands on the field with that `name` and shows in its `ui:field.errorText`.

A server error belongs to the value that caused it. It disappears when the user changes the value and returns when they enter that value again. Pick the VIP ticket in the [event registration example](#content-complete-example-event-registration) and submit to see it.

A key equal to the `objectName` carries a message about the whole object. It becomes the error text of the form and the form ends in `error`.

### Reject a submission from the action

For a rule that lives outside the model, such as a sold-out ticket, send the `422` yourself:

```php
if ($eventRegistration->getTicketType() === 'vip') {
    $payload = ['eventRegistration.ticketType' => ['VIP tickets are sold out.']];
    $response = $this->jsonResponse(json_encode($payload, JSON_THROW_ON_ERROR))->withStatus(422);
}
```

## Secure the action

A `Form` posts with `fetch` and the cookies of the visitor, like any other form. Extbase signs part of what it sends, the rest is up to your action:

- `__trustedProperties` signs the field names, not the visitor. Name the properties an argument may set with `allowProperties('name', 'email')`, because `allowAllProperties()` lets a visitor set every property of the object.
- `__identity` is signed as present, its value is not. A visitor can send the identity of any object of that type, so check in the action that they may change it.
- The frontend session cookie is `SameSite=lax` by default, so another site cannot post a form with the session of the visitor. If your site sets `cookieSameSite` to `none`, send a request token: pass `{ headers: { 'X-TYPO3-RequestToken': token } }` to [`extbase.post()`](/docs/client-utilities/extbase) and read it with `SecurityAspect::getReceivedRequestToken()` in the action.
- `required`, `pattern`, `maxlength`, `accept` and the client-side validation below only help the visitor. Validate on the server.

## Client-side validation

Validating in the browser gives quicker feedback and improves the user experience. Keep the server-side validation as well: a client check can be bypassed, the server check cannot. Native constraints, the `validation` of the form and the `validate` of a field end up in the same error text as the errors of the server.

Fields check the native constraints of their control without any TypeScript: `required`, `type="email"`, `pattern`, `min`/`max` and `minlength`/`maxlength`. A `maxLength` that should not cut a paste off works through the [`maxLengthBehavior`](/docs/components/input#content-allow-typing-past-the-limit) of an Input or Textarea. Submit this form empty to see the messages:

{% component: "ui:componentExample", arguments: { "componentName": "FieldExamples.nativeValidation" } %}

By default a field validates when the user leaves it after editing, and again on every change while it shows an error. A submit validates every field and moves the focus to the first invalid one. The [validation mode](/docs/components/field#content-validation-mode) changes when errors show up, and a field can run its own [async check](/docs/components/field#content-async-validation).

### Validate with a schema

Pass a synchronous Standard Schema validator such as Zod (`npm install zod`) as `validation`, next to your `onSubmit`:

```typescript
import { z } from 'zod';

const validation = z.object({
    name: z.string().min(1, 'Please enter your name'),
    email: z.email('Please enter a valid email address'),
    ticketCount: z.coerce.number().min(1).max(10),
    privacy: z.literal('1', 'Please accept the privacy policy'),
});

mount('ui:form', 'registration-form', ({ props }) => new Form({ ...props, validation }).init());
```

The keys match the `name` of the fields, `person.name` for a nested one. Values arrive as strings or `File`s, so `ticketCount` reads as `'2'` until `z.coerce.number()` converts it. `onSubmit` runs once every field is valid, until then the focus moves to the first invalid field. Any validator that implements the Standard Schema interface works the same way.

### Manual client-side validation

Without a schema library, pass a callback. It receives `values` and returns errors keyed by field name, the shape of a `422` response. A callback also suits rules across fields, like a password confirmation.

```typescript
mount('ui:form', 'registration-form', ({ props }) =>
    new Form({
        ...props,
        validation: ({ values }) => {
            const errors: Record<string, { messages: string[] }> = {};
            const email = values.get('email');

            if (typeof email !== 'string' || !email.includes('@')) {
                errors.email = { messages: ['Please enter a valid email address.'] };
            }

            return errors;
        },
    }).init()
);
```

`values` offers `get(path)`, `getAll(path)`, `has(path)`, `pick(path)` and `toObject()`, with your field names as dot paths. `validation` runs synchronously. Check a single field asynchronously with the `validate` of the field, and use `onSubmit` for checks that need the whole form.

## Show the result of a submit

`onSubmit` ends with one of three results:

- `true` - the form goes to `success`
- `false` - the form goes to `error`, as does a rejected promise such as a network failure
- field errors, `Record<string, { messages: string[] }>` - the form goes to `invalid` and every message lands on its field

### Show a message for the whole form

Set the text with `api.setErrorText()` and `api.setSuccessText()`, as the `onSubmit` above does. `form.content` shows its fields in `ready`, `invalid` and `submitting` and hides them in `error` and `success`. A `form.indicator` shows its content in the state you give it. `form.errorText` and `form.successText` show the text you set, or their own content when you set none. A button with `type="reset"` brings the form back to `ready`.

```html
<ui:form.root ...>
    <ui:form.content>
        <!-- fields -->
    </ui:form.content>

    <ui:form.indicator state="{f:constant(name: 'Jramke\FluidPrimitives\Enum\FormState::Error')}">
        <ui:form.errorText>Something went wrong. Please try again.</ui:form.errorText>
        <ui:button type="reset">Back to form</ui:button>
    </ui:form.indicator>

    <ui:form.indicator state="{f:constant(name: 'Jramke\FluidPrimitives\Enum\FormState::Success')}">
        <ui:form.successText
            >Your registration was submitted successfully. Thank you!</ui:form.successText
        >
    </ui:form.indicator>
</ui:form.root>
```

### Map a response to field errors

A response other than a `422` can end up on a field too. Return the errors from `onSubmit` after the `extbase.post()` call:

```typescript
if (response.status === 409) {
    return { email: { messages: ['This email address is already registered.'] } };
}

return response.ok;
```

### Style the form state

The `<form>` carries a `data-state` attribute: `ready`, `submitting`, `invalid`, `success` or `error`. `data-submitting`, `data-invalid`, `data-dirty` and `data-touched` are present while they apply.

```css
form[data-submitting] button[type='submit'] {
    opacity: 0.5;
    pointer-events: none;
}
```

## Submit button

Use the [Button](/docs/components/button) as the submit button. It shows a loading state while the form submits: `focusableWhenDisabled` keeps the focus on it and its `indicator` parts swap the label. [Loading States](/docs/components/button#content-loading-states) explains the `aria-labelledby` wrapper.

```html
<f:variable name="submitLabelId" value="{ui:id()}" />
<ui:button
    type="submit"
    focusableWhenDisabled="{true}"
    aria-labelledby="{submitLabelId}"
    autoMount="{false}"
    rootId="submit-button"
>
    <span id="{submitLabelId}">
        <ui:button.indicator
            state="{f:constant(name: 'Jramke\FluidPrimitives\Enum\ButtonIndicatorState::Idle')}"
            >Register</ui:button.indicator
        >
        <ui:button.indicator
            state="{f:constant(name: 'Jramke\FluidPrimitives\Enum\ButtonIndicatorState::Loading')}"
            >Submitting...</ui:button.indicator
        >
    </span>
</ui:button>
```

Mount the button with its `rootId` and pass the submitting state on from the `render` callback of the form, which runs whenever the form state changes:

```typescript
import { Button } from 'fluid-primitives/button';

const submitButton = mount('ui:button', 'submit-button', ({ props }) => new Button(props).init());

mount('ui:form', 'registration-form', ({ props }) =>
    new Form({
        ...props,
        render: form => submitButton?.api.setLoading(form.api.isSubmitting),
    }).init()
);
```

## Complete example: event registration

A registration with a ticket select, a country combobox, an optional badge photo and Zod validation on the client. The student ticket shows a student ID field through [`listenTo`](/docs/components/field#content-show-a-field-depending-on-another-field), and the VIP ticket is sold out on the server.

{% component: "ui:componentExample", arguments: { "componentName": "EventRegistration", "additionalFiles": {"EventRegistration.entry.ts": "EXT:docs/Resources/Private/Components/EventRegistration/EventRegistration.entry.ts", "EventRegistrationController.php": "EXT:docs/Classes/Controller/EventRegistrationController.php", "EventRegistration.php": "EXT:docs/Classes/Domain/Model/EventRegistration.php", "EventRegistrationPerson.php": "EXT:docs/Classes/Domain/Model/EventRegistrationPerson.php", "EventRegistrationValidator.php": "EXT:docs/Classes/Domain/Validator/EventRegistrationValidator.php"} } %}
