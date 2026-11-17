# Select

**A dropdown for choosing a predefined value, or several.**

{% component: "ui:referenceButtons", arguments: { "name": "Select" } %}

{% component: "ui:componentExample", arguments: { "componentName": "SelectExamples.simple", "withEntryFile": true } %}

## Features

- Single and multiple selection
- Typeahead focuses items as you type
- Keyboard navigation with arrow keys, home and end
- Disabled items and groups
- Hosts other widgets, like tabs or a filter input, next to the list with the `dialog` popup type
- Filters the items while you type, with an empty state for no matches
- Works with the Field component
- Custom positioning

## Anatomy

Place the options inside `list`, which carries the listbox semantics, focus and scrolling. `content` wraps it and is announced as a dialog with the `dialog` popup type. The styled `ui:select.content` combines `positioner` and `content` in one part, so you nest `ui:select.list` in it yourself:

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
            <primitives:select.input />
            <primitives:select.list>
                <primitives:select.item>
                    <primitives:select.itemText />
                    <primitives:select.itemIndicator />
                </primitives:select.item>
                <primitives:select.itemGroup>
                    <primitives:select.itemGroupLabel />
                </primitives:select.itemGroup>
            </primitives:select.list>
            <primitives:select.empty />
        </primitives:select.content>
    </primitives:select.positioner>
    <primitives:select.hiddenSelect />
</primitives:select.root>
```

## Installation

{% component: "ui:installationSection", arguments: { "name": "Select" } %}

## Examples

### Default value

Pass `defaultValue` as a list to preselect an item. The demo opens with Banana selected.

{% component: "ui:componentExample", arguments: { "componentName": "SelectExamples.defaultValue" } %}

### Open over the trigger

The styled `ui:select.root` opens the list over the trigger, with the selected item right on top of its value, as a native select on macOS does. A long list scrolls so that item is in place. Touch input and a lack of room fall back to the list below the trigger. The primitive `primitives:select.root` keeps the default of Zag.js, `alignItemWithTrigger="{false}"`.

{% component: "ui:componentExample", arguments: { "componentName": "SelectExamples.alignItemWithTrigger" } %}

### Open below the trigger

Set `alignItemWithTrigger="{false}"` to open the list below the trigger, where `positioning` places it.

{% component: "ui:componentExample", arguments: { "componentName": "SelectExamples.belowTrigger" } %}

### Multiple selection

Set `multiple="{true}"` to select several items. `closeOnSelect="{false}"` keeps the list open while you pick them.

{% component: "ui:componentExample", arguments: { "componentName": "SelectExamples.multiple" } %}

### Disabled items

Set `disabled: true` on an item of the collection to make it unselectable.

{% component: "ui:componentExample", arguments: { "componentName": "SelectExamples.disabledItems" } %}

### With item groups

Set `groupByKey` on `ui:listCollection` to group the items, here by their `type`, and render an `itemGroup` with its label per group.

{% component: "ui:componentExample", arguments: { "componentName": "SelectExamples.withGroups" } %}

### With form field

Wrap the select in `ui:field.root` so it takes the field's `name`, `required` and invalid state. The demo is a country select for a shipping address.

{% component: "ui:componentExample", arguments: { "componentName": "SelectExamples.withField" } %}

### Filter the items

Set `popupType="{f:constant(name: 'Jramke\FluidPrimitives\Enum\PopupType::Dialog')}"`, put a `select.input` above the `select.list` and a `select.empty` below it. The input takes focus when the select opens and keeps it while the arrow keys move the highlight. Typing filters the items by their label, and the `empty` part shows when nothing matches.

{% component: "ui:componentExample", arguments: { "componentName": "SelectExamples.filterable" } %}

The registry entry file filters with `onInputValueChange`. Replace it to filter your own way: call `getSourceCollection()` for the full collection and pass the result to `updateProps({ collection })`.

### With tabs

Set `popupType="{f:constant(name: 'Jramke\FluidPrimitives\Enum\PopupType::Dialog')}"` when the popup holds more than the list. `content` is then announced as a dialog, `list` stays the listbox, and you place the other widgets next to the list inside `content`. In the demo, tabs narrow the produce down to fruits or vegetables.

{% component: "ui:componentExample", arguments: { "componentName": "SelectExamples.withTabs", "additionalFiles": {"WithTabs.entry.ts": "EXT:docs/Resources/Private/Components/ui/SelectExamples/WithTabs.entry.ts"} } %}

### Localization

The label of the clear button ships in the language file and follows the site language. Pass `translations` to override it for one select.

```html
<ui:select.root
    collection="{countries}"
    translations="{
        clearTriggerLabel: f:translate(key: 'LLL:EXT:my_extension/Resources/Private/Language/locallang.xlf:select.clear')
    }"
>
    ...
</ui:select.root>
```

A screen reader announces the highlighted item through a live region, and the texts ship in the language file too. `itemAnnouncement` sets that text and `itemAnnouncementSelected` the one for the selected item, both with `%label%` and `%value%` placeholders, e.g. `itemAnnouncementSelected: '%label%, ausgewählt'`. A client script passes `itemAnnouncement` as a function of `{ value, label, selected }` instead.

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
            ["input", "Filters the items while you type. Renders an `<input>` element, for the `dialog` popup type."],
            ["empty", "Tells that no item matches. Renders a `<div>` that stays on the page as a live region, with a `<div>` inside it that takes your `class` and attributes and is hidden while an item is visible."],
            ["list", "The listbox inside `content` that holds the options - it takes focus, scrolls and tracks the active option. Renders a `<div>` element."],
            ["itemGroup", "Groups related options together. Renders a `<div>` element."],
            ["itemGroupLabel", "Labels a group of related options. Renders a `<div>` element."],
            ["item", "Represents a selectable option. Renders a `<div>` element."],
            ["itemText", "Displays the text content of an option. Renders a `<span>` element."],
            ["itemIndicator", "Displays the selected-state indicator for an option. Renders a `<div>` element."],
            ["hiddenSelect", "Provides the native `<select>` element for form submission. Renders a `<select>` element, with the empty-value input that submits `''` while nothing is selected in front of it."]
        ]
    }
%}
