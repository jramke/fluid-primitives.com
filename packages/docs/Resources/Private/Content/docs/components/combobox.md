# Combobox

**An input with a popup list for searching and selecting values from a collection.**

{% component: "ui:referenceButtons", arguments: { "name": "Combobox" } %}

{% component: "ui:componentExample", arguments: { "componentName": "ComboboxExamples.simple", "withEntryFile": true } %}

## Features

- Searchable list with keyboard navigation
- Single and multiple selection
- Disabled items and item groups
- Works with the Field component for forms and validation
- Submits the `value` of the selected items through `hiddenInput`, the visible input shows their labels
- `empty` placeholder for a search without matches
- Locale-aware filtering in the default entry file, replaceable per instance through `onInputValueChange`
- Async, server-rendered search results via `ui:template`
- `dialog` popup type moves the input into the popup, behind a focusable trigger

## Anatomy

`hiddenInput` submits the `value` of the selected items. Place it once anywhere inside `combobox.root` and it renders one visually hidden input per selected value, or a single empty one while nothing is selected. `ui:combobox.root` includes it for you. A [Field](/docs/components/field) focuses the combobox through that input when it is the first invalid field of a submitted form, and the input hands the focus on to the visible one.

Place the options inside `list`, which carries the listbox semantics and scrolling. `content` wraps it and is announced as a dialog with the `dialog` popup type. The styled `ui:combobox.content` combines `positioner` and `content` in one part, so you nest `ui:combobox.list` in it yourself.

`combobox.empty` is a placeholder for a collection with nothing to show, because no item matches the search text or an async search is still waiting for results. Place it next to `combobox.list` inside `content`. It shows and hides itself.

```html
<primitives:combobox.root>
    <primitives:combobox.label />
    <primitives:combobox.control>
        <primitives:combobox.input />
        <primitives:combobox.clearTrigger />
        <primitives:combobox.trigger />
    </primitives:combobox.control>
    <primitives:combobox.positioner>
        <primitives:combobox.content>
            <primitives:combobox.empty />
            <primitives:combobox.list>
                <primitives:combobox.item>
                    <primitives:combobox.itemText />
                    <primitives:combobox.itemIndicator />
                </primitives:combobox.item>
                <primitives:combobox.itemGroup>
                    <primitives:combobox.itemGroupLabel />
                </primitives:combobox.itemGroup>
            </primitives:combobox.list>
        </primitives:combobox.content>
    </primitives:combobox.positioner>
    <primitives:combobox.hiddenInput />
</primitives:combobox.root>
```

## Installation

{% component: "ui:installationSection", arguments: { "name": "Combobox" } %}

## Examples

### Default value

Pass `defaultValue` to preselect an item. The demo opens with Nigeria in the input, rendered on the server.

{% component: "ui:componentExample", arguments: { "componentName": "ComboboxExamples.defaultValue" } %}

### Disabled items

Set `disabled: true` on an item of the collection to make it unselectable. Oracle Database and Microsoft SQL Server are disabled in the demo.

{% component: "ui:componentExample", arguments: { "componentName": "ComboboxExamples.disabledItems" } %}

### With item groups

Set `groupByKey` on `ui:listCollection` to group the items, here by their `category`. Type in the demo and groups without a match disappear.

{% component: "ui:componentExample", arguments: { "componentName": "ComboboxExamples.withGroups" } %}

### With form field

Wrap the combobox in `ui:field.root` to share label, `name`, required and invalid state. The demo is a required country field.

{% component: "ui:componentExample", arguments: { "componentName": "ComboboxExamples.withField" } %}

### Multiple selection

Set `multiple="{true}"` to select several values. The demo lists the selected cities above the input.

{% component: "ui:componentExample", arguments: { "componentName": "ComboboxExamples.multiple" } %}

### Change the filter

Pass your own `onInputValueChange` in a custom entry file to change how items match, here by the start of the label with `startsWith`. Type "b" in the demo and the list shrinks to the cities starting with B.

{% component: "ui:componentExample", arguments: { "componentName": "ComboboxExamples.customFilterApi" } %}

### Async search

Fetch matching items from a server-side search endpoint as people type. Type a city in the demo to see the results come in.

Write the item markup once inside a [`ui:template`](/docs/viewhelpers/template) block and pass the endpoint as `searchUrl`. The entry file queries it, clones the template for every result and rebuilds the collection. `combobox.item`, `combobox.itemText` and `combobox.itemIndicator` detect the template on their own, so they take no `value`. `collection` is optional when no items are known up front.

`combobox.empty` shows whenever no item is rendered: before the first query, while a request runs, after an error and for a query without matches. Its content is yours to update, here a spinner and a status text. They are slot content, so their `ui:ref` takes `context: 'combobox'`, as in `{ui:ref(name: 'statusSpinner', context: 'combobox')}`.

The demo sends the query with [`extbase.post()`](/docs/client-utilities/extbase), fetches with [`AsyncList`](/docs/client-utilities/async-list) and delays the spinner with a [`DelayedIndicator`](/docs/client-utilities/delayed-indicator) to avoid flicker on fast responses.

{% component: "ui:componentExample", arguments: { "componentName": "ComboboxExamples.asyncSearch", "additionalFiles": {"AsyncSearch.entry.ts": "EXT:docs/Resources/Private/Components/ui/ComboboxExamples/AsyncSearch.entry.ts"} } %}

### Async search with groups

Group the results the endpoint returns, here by country. Write a second `ui:template` for the group (`combobox.itemGroup` with `combobox.itemGroupLabel`), clone it once per group, append the cloned items to the group and the group to `combobox.list`. Pass the group key as the `value` of its [`Template`](/docs/client-utilities/template) so the groups stay apart.

{% component: "ui:componentExample", arguments: { "componentName": "ComboboxExamples.asyncSearchGrouped", "additionalFiles": {"AsyncSearchGrouped.entry.ts": "EXT:docs/Resources/Private/Components/ui/ComboboxExamples/AsyncSearchGrouped.entry.ts"} } %}

### Move the input into the popup

Set `popupType="{f:constant(name: 'Jramke\FluidPrimitives\Enum\PopupType::Dialog')}"` for a popover-style picker: a trigger opens the popup and the `input` sits at the top of the `content`, above the `list`. `content` is announced as a dialog, and the trigger is focusable (its own `focusable` prop overrides that). With the input in the popup, the trigger shows the selection itself, here through a `valueText` element the entry file keeps in sync with `api.valueAsString`. Open the picker in the demo and type to filter.

{% component: "ui:componentExample", arguments: { "componentName": "ComboboxExamples.inputInContent", "additionalFiles": {"InputInContent.entry.ts": "EXT:docs/Resources/Private/Components/ui/ComboboxExamples/InputInContent.entry.ts"} } %}

### Localization

The labels of the trigger and the clear button ship in the language file and follow the site language. Pass `translations` to override them for one combobox.

```html
<f:variable
    name="comboboxTranslations"
    value="{
        triggerLabel: '{f:translate(key: \'LLL:EXT:my_extension/Resources/Private/Language/locallang.xlf:combobox.trigger\')}',
        clearTriggerLabel: '{f:translate(key: \'LLL:EXT:my_extension/Resources/Private/Language/locallang.xlf:combobox.clear\')}'
    }"
/>

<ui:combobox.root translations="{comboboxTranslations}"> ... </ui:combobox.root>
```

## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Combobox",
        "parts": [
            ["root", "Provides shared combobox state and wraps all related parts. Renders a `<div>` element."],
            ["label", "Labels the combobox. Renders a `<label>` element."],
            ["control", "Groups the input and its trigger/clear buttons. Renders a `<div>` element."],
            ["input", "The text input used to search and select from the collection. Renders an `<input>` element."],
            ["clearTrigger", "Clears the current input value and selection. Renders a `<button>` element."],
            ["trigger", "Opens and closes the combobox listbox. Not focusable unless `focusable` is set, or `popupType` is `dialog`. Renders a `<button>` element."],
            ["positioner", "Positions the floating combobox content. Renders a `<div>` element."],
            ["content", "The popup surface, positioned by `positioner`. Holds the `list` and `empty` and, with `popupType` `dialog`, the `input` too. Renders a `<div>` element."],
            ["list", "The listbox inside `content` that holds the options - it scrolls and carries `role=\"listbox\"`. Renders a `<div>` element."],
            ["empty", "Displays a placeholder for when no items match the current search. Renders a `<div>` element."],
            ["item", "Represents a selectable option. Renders a `<div>` element."],
            ["itemText", "Displays the text content of an option. Renders a `<div>` element."],
            ["itemIndicator", "Displays the selected-state indicator for an option. Renders a `<div>` element."],
            ["itemGroup", "Groups related options together. Renders a `<div>` element."],
            ["itemGroupLabel", "Labels a group of related options. Renders a `<div>` element."],
            ["hiddenInput", "Provides the real, submittable value(s) for the selected item(s), since the visible input only ever holds label text. Renders one `<input>` element per selected value, or one empty `<input>` while nothing is selected."]
        ]
    }
%}
