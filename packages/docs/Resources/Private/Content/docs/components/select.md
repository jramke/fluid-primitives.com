# Select

**A common form component for choosing a predefined value in a dropdown menu.**

{% component: "ui:referenceButtons", arguments: { "name": "Select" } %}

{% component: "ui:componentExample", arguments: { "componentName": "Select.examples.simple" } %}

## Features

- Support for single and multiple selection
- Typeahead to allow focusing items by typing text
- Keyboard navigation support including arrow keys, home/end
- Supports disabled items and groups
- Works with Field component for form integration
- Supports custom positioning

## Installation

{% component: "ui:installationSection", arguments: { "name": "Select" } %}

## Examples

### Default Value

Set an initial selected value.

{% component: "ui:componentExample", arguments: { "componentName": "Select.examples.defaultValue" } %}

### Multiple Selection

Allow selecting multiple items.

{% component: "ui:componentExample", arguments: { "componentName": "Select.examples.multiple" } %}

### Disabled Items

Disable specific items in the list.

{% component: "ui:componentExample", arguments: { "componentName": "Select.examples.disabledItems" } %}

### With Item Groups

Organize items into logical groups.

{% component: "ui:componentExample", arguments: { "componentName": "Select.examples.withGroups" } %}

### With Form Field

Use with the Field component for form validation.

{% component: "ui:componentExample", arguments: { "componentName": "Select.examples.withField" } %}

## Anatomy

```html
<primitives:select.root>
    <primitives:select.label />
    <primitives:select.control>
        <primitives:select.trigger>
            <primitives:select.valueText />
            <primitives:select.indicator />
        </primitives:select.trigger>
        <primitives:select.clearTrigger />
    </primitives:select.control>
    <primitives:select.positioner>
        <primitives:select.content>
            <primitives:select.item>
                <primitives:select.itemText />
                <primitives:select.itemIndicator />
            </primitives:select.item>
            <primitives:select.itemGroup>
                <primitives:select.itemGroupLabel />
            </primitives:select.itemGroup>
        </primitives:select.content>
    </primitives:select.positioner>
    <primitives:select.hiddenSelect />
</primitives:select.root>
```
