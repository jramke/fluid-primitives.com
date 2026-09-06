# Combobox

**An input with a popup listbox for searching and selecting values from a collection.**

{% component: "ui:referenceButtons", arguments: { "name": "Combobox" } %}

{% component: "ui:componentExample", arguments: { "componentName": "Combobox.examples.simple", "withEntryFile": true } %}

## Features

- Searchable listbox with keyboard navigation
- Supports single and multiple selection
- Supports disabled items and item groups
- Works with the Field component for forms and validation
- Supports custom client-side filtering via `setFilter()`
- Uses locale-aware fallback filtering based on Zag's i18n utilities
- Supports async, server-rendered search results via `ui:template`

## Installation

{% component: "ui:installationSection", arguments: { "name": "Combobox" } %}

## Examples

### Default Value

Set an initial selected value and render its label into the input on first paint.

{% component: "ui:componentExample", arguments: { "componentName": "Combobox.examples.defaultValue" } %}

### Multiple Selection

Allow selecting multiple items. In this mode the input is cleared after each selection.

{% component: "ui:componentExample", arguments: { "componentName": "Combobox.examples.multiple" } %}

### Disabled Items

Mark specific options as unavailable.

{% component: "ui:componentExample", arguments: { "componentName": "Combobox.examples.disabledItems" } %}

### With Item Groups

Organize items into labeled groups.

{% component: "ui:componentExample", arguments: { "componentName": "Combobox.examples.withGroups" } %}

### With Form Field

Use the combobox inside `Field` to share label, name, required and invalid state.

{% component: "ui:componentExample", arguments: { "componentName": "Combobox.examples.withField" } %}

### Custom Filter API

Use `setFilter()` in a custom mount entry when you want to override filtering imperatively per instance.

{% component: "ui:componentExample", arguments: { "componentName": "Combobox.examples.customFilterApi" } %}

### Async Search

Load items from a server-side search endpoint as the user types, instead of rendering the full collection up front.

Author the item's markup once inside a `ui:template` block - it makes `ui:ref` work on plain, hand-authored elements even though they're technically slot content, not a component's own template body. `combobox.item`, `combobox.itemText`, and `combobox.itemIndicator` all detect they're inside a `ui:template` block automatically - so we dont need to pass a `value` prop. On the client, clone the template per search result, populate its `ui:ref`'d elements directly, and rebuild the collection. Fetching, debouncing, and race-condition handling are left to your own code, typically built on `@zag-js/async-list`.

`collection` can be omitted entirely for a combobox with no server-known items at all - it's optional and defaults to empty regardless of `searchUrl`.

The example below sends the search query via [`extbase.post()`](/docs/utilities/extbase) rather than a GET param, sidestepping a `cHash` mismatch `f:uri.action`'s URL would otherwise hit, and drives its loading/error/empty status placeholder off a single [`DelayedIndicator`](/docs/utilities/delayed-indicator) so the spinner and status text can never disagree.

{% component: "ui:componentExample", arguments: { "componentName": "Combobox.examples.asyncSearch", "additionalFiles": {"AsyncSearch.entry.ts": "EXT:docs/Resources/Private/Components/ui/Combobox/Examples/AsyncSearch.entry.ts"} } %}

### Async Search with Groups

Async results can be grouped too - author a second `ui:template` for the group wrapper (`combobox.itemGroup`/`combobox.itemGroupLabel`), clone one per group returned by your search, and append the item clones into it instead of directly into `combobox.content`.

`combobox.itemGroup` needs a unique `data-id` per instance so `Combobox` can tell groups apart - the same thing `ui:id()` gives a server-rendered group, done client-side with `uid()`. Nothing about `Combobox`'s own rendering needed to change for this: it already looks up every `[data-part="item-group"]` element independently and reads its `data-id` fresh on every render, whether that element was server-rendered or just cloned.

{% component: "ui:componentExample", arguments: { "componentName": "Combobox.examples.asyncSearchGrouped", "additionalFiles": {"AsyncSearchGrouped.entry.ts": "EXT:docs/Resources/Private/Components/ui/Combobox/Examples/AsyncSearchGrouped.entry.ts"} } %}

### Localization

Default combobox trigger labels are shipped via XLF and follow the current Site Language. For per-template overrides, pass translated strings through the `translations` prop. Set a translation entry to `{false}` or an empty string to omit the corresponding `aria-label`.

Note that Zag.js uses a function for the trigger label to allow dynamic labels based on the copied state. Fluid Primitives simplifies this by accepting static strings for both states, which are then merged into the appropriate function internally.

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

<!-- TODO -->
<!--
## API Reference

{%
    component: "ui:ComponentPropsTable",
    arguments: {
        "name": "Combobox",
        "parts": [
            ["root", "Provides dialog state and context for the composed parts. Renders no wrapper element."],
            ["trigger", "Opens the dialog. Renders a `<button>` element."],
            ["backdrop", "Displays the overlay behind the dialog content. Renders a `<div>` element."],
            ["positioner", "Positions the dialog content within the viewport. Renders a `<div>` element."],
            ["content", "Contains the dialog surface and interactive content. Renders a `<div>` element."],
            ["title", "Provides the accessible title for the dialog. Renders a `<div>` element."],
            ["description", "Provides supporting descriptive text for the dialog. Renders a `<div>` element."],
            ["closeTrigger", "Closes the dialog when activated. Renders a `<button>` element."]
        ]
    }
%} -->

## Anatomy

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
            <primitives:combobox.item>
                <primitives:combobox.itemText />
                <primitives:combobox.itemIndicator />
            </primitives:combobox.item>
            <primitives:combobox.itemGroup>
                <primitives:combobox.itemGroupLabel />
            </primitives:combobox.itemGroup>
        </primitives:combobox.content>
    </primitives:combobox.positioner>
</primitives:combobox.root>
```
