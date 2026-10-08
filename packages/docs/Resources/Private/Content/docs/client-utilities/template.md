# Template

**Clones a `<template>` part and fills it with data that doesn't exist when the server renders the page.**

Use it for anything the client adds after hydration: async search results, previews of uploaded files, or the rows of a recurring field. The markup of the item stays in your Fluid template, so it is styled and translated like the rest of the component.

## Usage

Declare the `<template>` with [`ui:template`](/docs/viewhelpers/template) inside the component. It needs exactly one root element:

```html
<ui:combobox.list>
    <ui:template name="itemTemplate" context="combobox">
        <ui:combobox.item>
            <ui:combobox.itemText>
                <span {ui:ref(name: 'title')}></span>
            </ui:combobox.itemText>
        </ui:combobox.item>
    </ui:template>
</ui:combobox.list>
```

Then clone it from the component with its hydrator, fill it and insert it:

```typescript
import { Template } from 'fluid-primitives';

for (const { value, title } of cities) {
    const instance = new Template(combobox.hydrator, 'itemTemplate', { value });

    const titleEl = instance.query<HTMLElement>('title');
    if (titleEl) titleEl.textContent = title;

    listEl.appendChild(instance);
}
```

`instance` is a `DocumentFragment`, so it can be appended as it is. `instance.root` keeps pointing at the item after the insertion has emptied the fragment.

With `value` the clone represents one real item: `data-value` is set on it and on the parts inside it that exist once per item, and independent components inside it, such as a `Field` with an `Input`, are prepared. Call the `mountAll()` of each name in `instance.componentNames` after you insert the clone.

## Options

{% component: "ui:apiReference", arguments: { "symbol": "TemplateOptions" } %}

## API

{% component: "ui:apiReference", arguments: { "symbol": "Template" } %}
