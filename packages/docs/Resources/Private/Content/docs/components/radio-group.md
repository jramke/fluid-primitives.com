# Radio Group

**An easily stylable radio button component.**

{% component: "ui:referenceButtons", arguments: { "name": "RadioGroup" } %}
{% component: "ui:radioGroup.examples.simple" %}

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
