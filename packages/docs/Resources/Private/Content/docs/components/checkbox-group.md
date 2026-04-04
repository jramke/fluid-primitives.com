# Checkbox Group

**A group of checkboxes for selecting multiple values.**

{% component: "ui:referenceButtons", arguments: { "name": "CheckboxGroup", "skipZag": true } %}

{% component: "ui:componentExample", arguments: { "componentName": "CheckboxGroup.examples.simple", "withEntryFile": true } %}

## Features

- Multiple selection support (unlike RadioGroup which is single selection)
- Optional maximum number of selections via `maxSelectedValues`
- Full keyboard navigation support
- Syncs with native form elements for proper form submission
- Works with Field component for form integration

## Installation

{% component: "ui:installationSection", arguments: { "name": "CheckboxGroup" } %}

## Examples

### Default Checked

Pre-select multiple options using an array of values.

{% component: "ui:componentExample", arguments: { "componentName": "CheckboxGroup.examples.defaultChecked" } %}

### Disabled Items

Disable specific checkbox options.

{% component: "ui:componentExample", arguments: { "componentName": "CheckboxGroup.examples.disabledItems" } %}

### Disabled Group

Disable the entire checkbox group.

{% component: "ui:componentExample", arguments: { "componentName": "CheckboxGroup.examples.disabledGroup" } %}

### Maximum Selections

Limit the number of selectable options. Once the limit is reached, remaining unchecked options are automatically disabled.

{% component: "ui:componentExample", arguments: { "componentName": "CheckboxGroup.examples.maxSelected" } %}

### Select All

Implement a "Select All" checkbox that toggles all options.

{% component: "ui:componentExample", arguments: { "componentName": "CheckboxGroup.examples.selectAll" } %}

## Anatomy

```html
<primitives:checkboxGroup.root>
    <primitives:checkboxGroup.label />
    <primitives:checkboxGroup.item>
        <primitives:checkboxGroup.itemControl>
            <primitives:checkboxGroup.itemIndicator />
        </primitives:checkboxGroup.itemControl>
        <primitives:checkboxGroup.itemText />
        <primitives:checkboxGroup.itemHiddenInput />
    </primitives:checkboxGroup.item>
</primitives:checkboxGroup.root>
```

## API Reference

### Root

The container for the checkbox group.

| Prop                | Type      | Default | Description                                       |
| ------------------- | --------- | ------- | ------------------------------------------------- |
| `defaultValue`      | `array`   | `[]`    | Array of values that should be checked by default |
| `name`              | `string`  | -       | Name attribute for form submission                |
| `form`              | `string`  | -       | Associates the group with a form element          |
| `disabled`          | `boolean` | `false` | Disables all checkboxes in the group              |
| `readOnly`          | `boolean` | `false` | Makes all checkboxes read-only                    |
| `required`          | `boolean` | `false` | Marks the group as required                       |
| `invalid`           | `boolean` | `false` | Marks the group as invalid                        |
| `maxSelectedValues` | `integer` | -       | Maximum number of checkboxes that can be selected |

### Item

An individual checkbox item.

| Prop       | Type      | Default      | Description                     |
| ---------- | --------- | ------------ | ------------------------------- |
| `value`    | `string`  | **required** | Unique value for this checkbox  |
| `disabled` | `boolean` | `false`      | Disables this specific checkbox |
| `invalid`  | `boolean` | `false`      | Marks this checkbox as invalid  |
