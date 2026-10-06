# Combobox

**An input with a popup listbox for searching and selecting values from a collection.**

{% component: "ui:referenceButtons", arguments: { "name": "Combobox" } %}

{% component: "ui:componentExample", arguments: { "componentName": "ComboboxExamples.simple", "withEntryFile": true } %}

## Features

- Searchable listbox with keyboard navigation
- Supports single and multiple selection
- Supports disabled items and item groups
- Works with the Field component for forms and validation
- Submits the selected item's `value` on form submit, not its label - the visible input only ever displays text, `hiddenInput` carries the real value(s)
- Supports an `empty` state placeholder shown automatically whenever no items match
- Filtering is plain `onInputValueChange` userland code, not a special API - swap the match logic per instance the same way you'd swap it in any Zag/Ark UI consumer
- Ships with locale-aware substring filtering via Zag's i18n utilities, wired up in the default mount entry
- Supports async, server-rendered search results via `ui:template`
- Can move the input into the popup, behind a focusable trigger, with the `dialog` popup type

## Installation

{% component: "ui:installationSection", arguments: { "name": "Combobox" } %}

## Examples

### Default Value

Set an initial selected value and render its label into the input on first paint.

{% component: "ui:componentExample", arguments: { "componentName": "ComboboxExamples.defaultValue" } %}

### Disabled Items

Mark specific options as unavailable.

{% component: "ui:componentExample", arguments: { "componentName": "ComboboxExamples.disabledItems" } %}

### With Item Groups

Organize items into labeled groups.

{% component: "ui:componentExample", arguments: { "componentName": "ComboboxExamples.withGroups" } %}

### With Form Field

Use the combobox inside `Field` to share label, name, required and invalid state.

{% component: "ui:componentExample", arguments: { "componentName": "ComboboxExamples.withField" } %}

### Multiple Selection

Allow users to select multiple values from the combobox.

{% component: "ui:componentExample", arguments: { "componentName": "ComboboxExamples.multiple" } %}

### Custom Filter Logic

Override the match logic per instance by passing your own `onInputValueChange` to a custom mount entry - swap `contains` for `startsWith`, or filter however you like.

{% component: "ui:componentExample", arguments: { "componentName": "ComboboxExamples.customFilterApi" } %}

### Async Search

Load items from a server-side search endpoint as the user types, instead of rendering the full collection up front.

Author the item's markup once inside a `ui:template` block - it makes `ui:ref` work on plain, hand-authored elements even though they're technically slot content, not a component's own template body. `combobox.item`, `combobox.itemText`, and `combobox.itemIndicator` all detect they're inside a `ui:template` block automatically - so we dont need to pass a `value` prop. On the client, clone the template per search result, populate its `ui:ref`'d elements directly, and rebuild the collection. Fetching, debouncing, and race-condition handling are left to your own code, typically built on `@zag-js/async-list`.

`collection` can be omitted entirely for a combobox with no server-known items at all - it's optional and defaults to empty regardless of `searchUrl`.

The status placeholder itself is just `combobox.empty` - `Combobox` already shows/hides it automatically whenever there are no items rendered, whatever the reason (no query typed yet, a request in flight, a failed request, or a query with zero matches). Only its _content_ - a spinner and a status text - is something the example's own code owns and updates; the loading/error messaging is entirely up to you. Both are plain, hand-authored elements passed `{ui:ref(name: 'statusSpinner', context: 'combobox')}` - since they're slot content rather than a component's own template body, `ui:ref` needs the explicit `context` argument to know which ancestor component to attach to, the same way `ui:template`'s own `context` argument does for the item markup below. The example below sends the search query via [`extbase.post()`](/docs/utilities/extbase) rather than a GET param, sidestepping a `cHash` mismatch `f:uri.action`'s URL would otherwise hit, and drives the spinner/status text off a single [`DelayedIndicator`](/docs/utilities/delayed-indicator) so they can never disagree.

{% component: "ui:componentExample", arguments: { "componentName": "ComboboxExamples.asyncSearch", "additionalFiles": {"AsyncSearch.entry.ts": "EXT:docs/Resources/Private/Components/ui/ComboboxExamples/AsyncSearch.entry.ts"} } %}

### Async Search with Groups

Async results can be grouped too - author a second `ui:template` for the group wrapper (`combobox.itemGroup`/`combobox.itemGroupLabel`), clone one per group returned by your search, and append the item clones into it instead of directly into `combobox.list`.

`combobox.itemGroup` needs a unique `data-id` per instance so `Combobox` can tell groups apart - the same thing `ui:id()` gives a server-rendered group, done client-side with `uid()`. Nothing about `Combobox`'s own rendering needed to change for this: it already looks up every `itemGroup` part independently and reads its `data-id` fresh on every render, whether that element was server-rendered or just cloned.

{% component: "ui:componentExample", arguments: { "componentName": "ComboboxExamples.asyncSearchGrouped", "additionalFiles": {"AsyncSearchGrouped.entry.ts": "EXT:docs/Resources/Private/Components/ui/ComboboxExamples/AsyncSearchGrouped.entry.ts"} } %}

### Input in Content

By default the input is the combobox's focus target and the popup is a passive listbox. Set `popupType` to `PopupType::Dialog` (`popupType="{f:constant(name: 'Jramke\FluidPrimitives\Enum\PopupType::Dialog')}"`) to turn it into a popover-style picker instead: the input moves into the `content`, above the `list`, and a trigger opens the popup. The `content` is then announced as a dialog, a click on the label focuses the trigger, and the trigger becomes focusable - it is the only way in, so Zag makes it so by default (the trigger's own `focusable` prop overrides that either way).

Since the input is no longer in the control, the trigger has to show the current selection itself - here a plain button with a hand-authored `valueText` ref that the example's entry file keeps in sync with `api.valueAsString`.

{% component: "ui:componentExample", arguments: { "componentName": "ComboboxExamples.inputInContent", "additionalFiles": {"InputInContent.entry.ts": "EXT:docs/Resources/Private/Components/ui/ComboboxExamples/InputInContent.entry.ts"} } %}

### Localization

Default combobox trigger labels are shipped via XLF and follow the current Site Language. For per-template overrides, pass translated strings through the `translations` prop.

```html
<f:variable
    name="comboboxTranslations"
    value="{
        triggerLabel: '{f:translate(key: \'LLL:EXT:site_package/Resources/Private/Language/locallang.xlf:combobox.trigger\')}',
        clearTriggerLabel: '{f:translate(key: \'LLL:EXT:site_package/Resources/Private/Language/locallang.xlf:combobox.clear\')}'
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
            ["hiddenInput", "Provides the real, submittable value(s) for the selected item(s), since the visible input only ever holds label text. Renders one `<input>` element per selected value."]
        ]
    }
%}

## Anatomy

Unlike `Select`, Zag's combobox machine renders no native form control of its own - the visible `input` only ever holds the highlighted item's label (or, with `allowCustomValue`, arbitrary typed text), never its `value`. `hiddenInput` fills that gap: one visually hidden text input (kept out of the accessibility tree and tab order via `aria-hidden`/`tabindex="-1"`, not `type="hidden"`) per selected value, kept in sync with the current selection so the item's `value` (not its label) is what actually gets submitted. Include it once anywhere inside `combobox.root` - it renders as many hidden inputs as there are selected values (zero, one, or - with `multiple` - several).

Zag puts the listbox semantics and scrolling on `list`, so the options belong inside `list` rather than directly inside `content`, which is a plain wrapper around it (a dialog, with the `dialog` popup type). The styled `ui:combobox.content` is the `positioner` and `content` in one part, and `ui:combobox.list` is the `list` - you place it inside the content yourself.

`combobox.empty` renders a placeholder for when the collection has no items to show - whether that's because nothing matches the current search text, or because an async search hasn't returned results yet. It's shown/hidden automatically alongside `content`/`list`'s own `data-empty` attribute, so no wiring is needed beyond placing it next to `combobox.list` inside `content` - it isn't an option, so it doesn't belong inside the listbox.

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
