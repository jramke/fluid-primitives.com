# Select

**A common form component for choosing a predefined value in a dropdown menu.**

{% component: "ui:referenceButtons", arguments: { "name": "Select" } %}

{% component: "ui:componentExample", arguments: { "componentName": "SelectExamples.simple", "withEntryFile": true } %}

## Features

- Support for single and multiple selection
- Typeahead to allow focusing items by typing text
- Keyboard navigation support including arrow keys, home/end
- Supports disabled items and groups
- Can host other widgets, like tabs, next to the list when the popup is a `dialog`
- Works with Field component for form integration
- Supports custom positioning

## Installation

{% component: "ui:installationSection", arguments: { "name": "Select" } %}

## Examples

### Default Value

Set an initial selected value.

{% component: "ui:componentExample", arguments: { "componentName": "SelectExamples.defaultValue" } %}

### Multiple Selection

Allow selecting multiple items.

{% component: "ui:componentExample", arguments: { "componentName": "SelectExamples.multiple" } %}

### Disabled Items

Disable specific items in the list.

{% component: "ui:componentExample", arguments: { "componentName": "SelectExamples.disabledItems" } %}

### With Item Groups

Organize items into logical groups.

{% component: "ui:componentExample", arguments: { "componentName": "SelectExamples.withGroups" } %}

### With Form Field

Use with the Field component for form validation.

{% component: "ui:componentExample", arguments: { "componentName": "SelectExamples.withField" } %}

### With Tabs

By default `content` is a passthrough wrapper around the `list`, and the list carries the listbox role. Set `popupType` to `PopupType::Dialog` (an enum, so `popupType="{f:constant(name: 'Jramke\FluidPrimitives\Enum\PopupType::Dialog')}"`) when the popup also holds other interactive widgets: `content` is then announced as a dialog, and `list` stays the listbox. Place those widgets next to the list inside `content`. Here a few tabs narrow the items down - the select keeps a single list, so the example moves it into the active tab's panel and swaps the machine's collection (hiding the other items) whenever the active tab changes.

{% component: "ui:componentExample", arguments: { "componentName": "SelectExamples.withTabs", "additionalFiles": {"WithTabs.entry.ts": "EXT:docs/Resources/Private/Components/ui/SelectExamples/WithTabs.entry.ts"} } %}

### Localization

Default clear trigger labels are shipped via XLF and follow the current Site Language. For per-template overrides, pass translated strings through the `translations` prop.

```html
<ui:select.root
    collection="{myCollection}"
    translations="{
        clearTriggerLabel: f:translate(key: 'LLL:EXT:site_package/Resources/Private/Language/locallang.xlf:select.clear')
    }"
>
    ...
</ui:select.root>
```

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Select",
        "parts": [
            ["root", "Provides shared select state and wraps all related parts. Renders a `<div>` element."],
            ["label", "Labels the select. Renders a `<label>` element."],
            ["control", "Groups the trigger and optional clear button. Renders a `<div>` element."],
            ["trigger", "Opens and closes the select menu. Renders a `<button>` element."],
            ["valueText", "Displays the selected value or placeholder text. Renders a `<span>` element."],
            ["indicator", "Displays a decorative indicator for the trigger. Renders a `<span>` element."],
            ["clearTrigger", "Clears the current selection. Renders a `<button>` element."],
            ["positioner", "Positions the floating select content. Renders a `<div>` element."],
            ["content", "The popup surface, positioned by `positioner`. Holds the `list` and, with `popupType` `dialog`, other widgets next to it. Renders a `<div>` element."],
            ["list", "The listbox inside `content` that holds the options - it takes focus, scrolls and tracks the active option. Renders a `<div>` element."],
            ["itemGroup", "Groups related options together. Renders a `<div>` element."],
            ["itemGroupLabel", "Labels a group of related options. Renders a `<div>` element."],
            ["item", "Represents a selectable option. Renders a `<div>` element."],
            ["itemText", "Displays the text content of an option. Renders a `<span>` element."],
            ["itemIndicator", "Displays the selected-state indicator for an option. Renders a `<div>` element."],
            ["hiddenSelect", "Provides the native `<select>` element for form submission. Renders a `<select>` element."]
        ]
    }
%}

## Anatomy

Zag puts the listbox semantics, focus, scrolling and the active option on `list`, so the options belong inside `list` rather than directly inside `content`, which is a plain wrapper around it (a dialog, with the `dialog` popup type). The styled `ui:select.content` is the `positioner` and `content` in one part, and `ui:select.list` is the `list` - you place it inside the content yourself:

```html
<ui:select.content>
    <ui:select.list>
        <ui:select.item>...</ui:select.item>
    </ui:select.list>
</ui:select.content>
```

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
            <primitives:select.list>
                <primitives:select.item>
                    <primitives:select.itemText />
                    <primitives:select.itemIndicator />
                </primitives:select.item>
                <primitives:select.itemGroup>
                    <primitives:select.itemGroupLabel />
                </primitives:select.itemGroup>
            </primitives:select.list>
        </primitives:select.content>
    </primitives:select.positioner>
    <primitives:select.hiddenSelect />
</primitives:select.root>
```
