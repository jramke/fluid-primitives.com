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

Author the item's markup once inside a `ui:template` block - it makes `ui:ref` work on plain, hand-authored elements even though they're technically slot content, not a component's own template body. On the client, clone the template per search result, populate its `ui:ref`'d elements directly, and rebuild the collection. Fetching, debouncing, and race-condition handling are left to your own code, typically built on `@zag-js/async-list`.

**TYPO3 cHash note:** `f:uri.action` computes its `cHash` from the arguments known at build time. If your client-side code appends a query parameter (e.g. `q`) to that URL afterward as a GET param, the request's parameter set no longer matches what was hashed, and TYPO3 rejects it with a 404. The simplest fix is to send the search query in a POST body instead - cHash only governs the cacheable GET query string, so it never comes into play:

```ts
const body = new URLSearchParams();
body.set('tx_yourext_yourplugin[q]', filterText);
await fetch(searchUrl, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body.toString(),
});
```

with a plain typed action argument on the PHP side (`searchAction(string $q = '')`) - Extbase maps it the same way for GET or POST. If you'd rather keep GET, exclude the parameter name from cHash calculation instead: `$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'q';`.

`collection` can be omitted entirely for a combobox with no server-known items at all - it's optional and defaults to empty regardless of `searchUrl`.

```html
<ui:combobox.root
    controlled="{true}"
    searchUrl="{f:uri.action(action: 'search', controller: 'YourSearch')}">
    <ui:combobox.control>
        <ui:combobox.input />
    </ui:combobox.control>
    <ui:combobox.content>
        <ui:template name="item-template" component="combobox">
            <ui:combobox.item renderedOnClient="{true}">
                <span {ui:ref(name: 'title', withId: false)}></span>
            </ui:combobox.item>
        </ui:template>
    </ui:combobox.content>
</ui:combobox.root>
```

{% component: "ui:componentExample", arguments: { "componentName": "Combobox.examples.asyncSearch", "additionalFiles": {"AsyncSearch.entry.ts": "EXT:docs/Resources/Private/Components/ui/Combobox/Examples/AsyncSearch.entry.ts"} } %}

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
