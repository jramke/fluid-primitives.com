# Field

**A form field wrapper that provides accessible labeling, error handling, and state management for form inputs.**

## Features

- Automatic label association with form controls
- Error message display with proper ARIA attributes
- Description text support for additional context
- State management for disabled, required, readonly, and invalid states
- Seamless integration with Form component for validation
- Works with all form-related primitives (Checkbox, Select, RadioGroup, etc.) and native inputs

## Installation

{% component: "ui:installationSection", arguments: { "name": "Field" } %}

## Usage

```html
<ui:field.root name="email" required="{true}">
    <ui:field.label>Email Address</ui:field.label>
    <ui:field.control asChild="{true}">
        <input type="email" class="input" />
    </ui:field.control>
    <ui:field.description>We'll never share your email.</ui:field.description>
    <ui:field.error />
</ui:field.root>
```

## Examples

### Basic Text Field

A simple text input with label and error handling.

```html
<ui:field.root name="username">
    <ui:field.label>Username</ui:field.label>
    <ui:field.control asChild="{true}">
        <input type="text" class="input" />
    </ui:field.control>
    <ui:field.error />
</ui:field.root>
```

### Required Field

Mark a field as required.

```html
<ui:field.root name="name" required="{true}">
    <ui:field.label>Full Name</ui:field.label>
    <ui:field.control asChild="{true}">
        <input type="text" class="input" />
    </ui:field.control>
    <ui:field.error />
</ui:field.root>
```

### With Description

Add helpful description text below the input.

```html
<ui:field.root name="password">
    <ui:field.label>Password</ui:field.label>
    <ui:field.control asChild="{true}">
        <input type="password" class="input" />
    </ui:field.control>
    <ui:field.description>Must be at least 8 characters long.</ui:field.description>
    <ui:field.error />
</ui:field.root>
```

### Disabled Field

Disable the entire field.

```html
<ui:field.root name="readonly-value" disabled="{true}">
    <ui:field.label>Disabled Input</ui:field.label>
    <ui:field.control asChild="{true}">
        <input type="text" class="input" value="Cannot be edited" />
    </ui:field.control>
</ui:field.root>
```

### With Checkbox

Use with the Checkbox component.

```html
<ui:field.root name="terms" required="{true}">
    <ui:checkbox.root>
        <ui:checkbox.control />
        <ui:checkbox.label>I accept the terms and conditions</ui:checkbox.label>
    </ui:checkbox.root>
    <ui:field.error />
</ui:field.root>
```

### With Select

Use with the Select component.

```html
<ui:listCollection
    items="{
        0: {value: 'us', label: 'United States'},
        1: {value: 'uk', label: 'United Kingdom'},
        2: {value: 'de', label: 'Germany'}
    }"
    as="collection"
/>

<ui:field.root name="country" required="{true}">
    <ui:select.root collection="{collection}">
        <ui:select.label>Country</ui:select.label>
        <ui:select.control>
            <ui:select.trigger placeholder="Select your country" />
        </ui:select.control>
        <ui:select.content>
            <f:for each="{collection.items}" as="item">
                <ui:select.item item="{item}">
                    <ui:select.itemText>{item.label}</ui:select.itemText>
                    <ui:select.itemIndicator />
                </ui:select.item>
            </f:for>
        </ui:select.content>
    </ui:select.root>
    <ui:field.error />
</ui:field.root>
```

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

<!--
## API Reference

### Field.Root

The root container that provides context for all field parts.

| Prop       | Type      | Default | Description                                               |
| ---------- | --------- | ------- | --------------------------------------------------------- |
| `name`     | `string`  | -       | **Required.** The field name for form submission.         |
| `disabled` | `boolean` | `false` | Whether the field is disabled.                            |
| `required` | `boolean` | `false` | Whether the field is required.                            |
| `readOnly` | `boolean` | `false` | Whether the field is read-only.                           |
| `invalid`  | `boolean` | `false` | Whether the field is invalid (overridden by form errors). |

### Field.Label

The label element associated with the field control.

### Field.Control

Wrapper for the actual form input. Use `asChild="{true}"` to pass props to your input element.

| Prop      | Type      | Default | Description                                      |
| --------- | --------- | ------- | ------------------------------------------------ |
| `asChild` | `boolean` | `false` | **Required.** Must be true to function properly. |

### Field.Description

Optional description text that provides additional context for the field.

### Field.Error

Container for error messages. Automatically populated when the field has validation errors from the Form component.

## Accessibility

- Label is automatically associated with the control via `htmlFor`
- Error messages are linked via `aria-describedby`
- Description text is linked via `aria-describedby`
- Invalid state is communicated via `aria-invalid`
- Required state is communicated via `aria-required`
- Error messages use `aria-live="polite"` for screen reader announcements

## Data Attributes

All parts expose data attributes for styling:

- `data-invalid` - Present when the field has validation errors
- `data-disabled` - Present when the field is disabled
- `data-readonly` - Present when the field is read-only
- `data-name` - Contains the field name -->
