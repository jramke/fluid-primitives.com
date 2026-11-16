# Translations

**Turns the texts of a `translations` prop into the functions a state machine wants.**

Many translations depend on state: the name of a file, the number of a page, the item a screen reader announces. A machine wants a function for those, but Fluid has no callbacks. A template writes a text with `%placeholders%` instead, a client script passes the function, and `resolveTranslations()` makes both the same function.

## Usage

In a template, write the text. The `%` form survives Fluid, a curly-brace placeholder would be read as a variable:

```html
<ui:pagination.root translations="{itemLabel: 'Page %page% of %totalPages%'}">
    ...
</ui:pagination.root>
```

In a client script, pass the function itself:

```typescript
import { mountAll } from 'fluid-primitives';
import { Pagination } from 'fluid-primitives/pagination';

mountAll('primitives:pagination', ({ props }) =>
    new Pagination({
        ...props,
        translations: {
            ...props.translations,
            itemLabel: ({ page, totalPages }) => `Page ${page} of ${totalPages}`,
        },
    }).init()
);
```

## Build a component with translations

Pass the `translations` prop and the keys that are functions to `resolveTranslations()`. Each key lists what fills its placeholders from the details the machine calls it with. Keys that are not listed stay texts, anything else is left out so the machine keeps its default.

```typescript
import { resolveTranslations } from 'fluid-primitives';

const translations = resolveTranslations(props.translations, {
    itemLabel: (details: { page: number; totalPages: number }) => ({ ...details }),
});
```

Use `fillPlaceholders()` where you build the text yourself. A placeholder without a value stays as written.

## API Reference

{% component: "ui:apiReference", arguments: { "symbol": "resolveTranslations" } %}

{% component: "ui:apiReference", arguments: { "symbol": "fillPlaceholders" } %}

{% component: "ui:apiReference", arguments: { "symbol": "TranslationText" } %}

{% component: "ui:apiReference", arguments: { "symbol": "PlaceholderValues" } %}
