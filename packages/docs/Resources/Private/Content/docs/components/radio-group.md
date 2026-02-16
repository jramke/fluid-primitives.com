# Radio Group

**An easily stylable radio button component.**

{% component: "ui:referenceButtons", arguments: { "name": "RadioGroup" } %}
{% component: "ui:radioGroup.examples.simple" %}

## Features

- Full keyboard navigation support
- Supports horizontal and vertical orientations
- Syncs with native form elements for proper form submission
- Works with Field component for form integration

## Installation

{% component: "ui:installationSection", arguments: { "name": "RadioGroup" } %}

## Usage

```html
<ui:radioGroup.root defaultValue="option1">
    <ui:radioGroup.label>Choose an option:</ui:radioGroup.label>
    <ui:radioGroup.item value="option1">
        <ui:radioGroup.itemControl />
        <ui:radioGroup.itemText>Option 1</ui:radioGroup.itemText>
    </ui:radioGroup.item>
    <ui:radioGroup.item value="option2">
        <ui:radioGroup.itemControl />
        <ui:radioGroup.itemText>Option 2</ui:radioGroup.itemText>
    </ui:radioGroup.item>
</ui:radioGroup.root>
```

## Examples

### Horizontal Orientation

Display radio items in a horizontal row.

```html
<ui:radioGroup.root orientation="horizontal" defaultValue="small">
    <ui:radioGroup.label>Size:</ui:radioGroup.label>
    <ui:radioGroup.item value="small">
        <ui:radioGroup.itemControl />
        <ui:radioGroup.itemText>Small</ui:radioGroup.itemText>
    </ui:radioGroup.item>
    <ui:radioGroup.item value="medium">
        <ui:radioGroup.itemControl />
        <ui:radioGroup.itemText>Medium</ui:radioGroup.itemText>
    </ui:radioGroup.item>
    <ui:radioGroup.item value="large">
        <ui:radioGroup.itemControl />
        <ui:radioGroup.itemText>Large</ui:radioGroup.itemText>
    </ui:radioGroup.item>
</ui:radioGroup.root>
```

### Disabled Items

Disable specific radio options.

```html
<ui:radioGroup.root defaultValue="available">
    <ui:radioGroup.label>Select plan:</ui:radioGroup.label>
    <ui:radioGroup.item value="available">
        <ui:radioGroup.itemControl />
        <ui:radioGroup.itemText>Available Plan</ui:radioGroup.itemText>
    </ui:radioGroup.item>
    <ui:radioGroup.item value="unavailable" disabled="{true}">
        <ui:radioGroup.itemControl />
        <ui:radioGroup.itemText>Unavailable Plan</ui:radioGroup.itemText>
    </ui:radioGroup.item>
</ui:radioGroup.root>
```

### Disabled Group

Disable the entire radio group.

```html
<ui:radioGroup.root disabled="{true}" defaultValue="option1">
    <ui:radioGroup.label>Disabled group:</ui:radioGroup.label>
    <ui:radioGroup.item value="option1">
        <ui:radioGroup.itemControl />
        <ui:radioGroup.itemText>Option 1</ui:radioGroup.itemText>
    </ui:radioGroup.item>
    <ui:radioGroup.item value="option2">
        <ui:radioGroup.itemControl />
        <ui:radioGroup.itemText>Option 2</ui:radioGroup.itemText>
    </ui:radioGroup.item>
</ui:radioGroup.root>
```

### With Form Field

Use with the Field component for form validation.

```html
<ui:field.root name="subscription" required="{true}">
    <ui:radioGroup.root>
        <ui:radioGroup.label>Subscription type:</ui:radioGroup.label>
        <ui:radioGroup.item value="monthly">
            <ui:radioGroup.itemControl />
            <ui:radioGroup.itemText>Monthly</ui:radioGroup.itemText>
        </ui:radioGroup.item>
        <ui:radioGroup.item value="yearly">
            <ui:radioGroup.itemControl />
            <ui:radioGroup.itemText>Yearly</ui:radioGroup.itemText>
        </ui:radioGroup.item>
    </ui:radioGroup.root>
    <ui:field.error />
</ui:field.root>
```

## Anatomy

```html
<primitives:radioGroup.root>
    <primitives:radioGroup.item>
        <primitives:radioGroup.itemControl />
        <primitives:radioGroup.itemText />
        <primitives:radioGroup.itemHiddenInput />
    </primitives:radioGroup.item>
    <primitives:radioGroup.indicator />
</primitives:radioGroup.root>
```
