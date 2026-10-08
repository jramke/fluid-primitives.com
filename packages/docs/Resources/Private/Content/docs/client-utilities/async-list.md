# AsyncList

**Loads, filters and pages a list of items asynchronously, without any DOM. A thin wrapper around the machine of `@zag-js/async-list`.**

`AsyncList` has no rendering of its own: you create it with a `load` function, subscribe to it and render the items yourself. The Combobox [Async Search](/docs/components/combobox) example uses it to fill the list from an Extbase action while the user types.

## Usage

```typescript
import { debounce } from '@zag-js/utils';
import { AsyncList, extbase } from 'fluid-primitives';

interface City {
    value: string;
    title: string;
}

const list = new AsyncList<City>({
    load: async ({ filter, signal }) => {
        const response = await extbase.post(searchUrl, { q: filter }, { signal });
        return { items: (await response.json()) as City[] };
    },
});

list.subscribe(api => {
    renderItems(api.items);
    spinnerEl.hidden = !api.isLoading;
});

const setFilter = debounce((filter: string) => list.setFilter(filter), 300);
inputEl.addEventListener('input', () => setFilter(inputEl.value));

list.init();
```

`setFilter()` reloads the list straight away. Debouncing it is up to you, so you decide the delay and can skip it where it isn't needed, e.g. when the list only reloads from `dependencies`. Call `list.destroy()` when you are done with it.

## Options

{% component: "ui:apiReference", arguments: { "symbol": "AsyncListOptions" } %}

## API

{% component: "ui:apiReference", arguments: { "symbol": "AsyncList" } %}

## The api object

`list.api` and the argument of a `subscribe()` listener are the api of the machine:

{% component: "ui:ComponentPropsTable", arguments: { "name": "AsyncList", "parts": [] } %}
