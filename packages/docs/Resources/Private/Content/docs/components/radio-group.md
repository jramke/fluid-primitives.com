# Radio Group

**An easily stylable radio button component.**

{% component: "ui:referenceButtons", arguments: { "name": "RadioGroup" } %}

{% component: "ui:componentExample", arguments: { "componentName": "RadioGroup.examples.simple", "withEntryFile": true } %}

## Features

- Full keyboard navigation support
- Supports horizontal and vertical orientations
- Syncs with native form elements for proper form submission
- Works with Field component for form integration

## Installation

{% component: "ui:installationSection", arguments: { "name": "RadioGroup" } %}

## Examples

### Disabled Items

Disable specific radio options.

{% component: "ui:componentExample", arguments: { "componentName": "RadioGroup.examples.disabledItems" } %}

### Disabled Group

Disable the entire radio group.

{% component: "ui:componentExample", arguments: { "componentName": "RadioGroup.examples.disabledGroup" } %}

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
