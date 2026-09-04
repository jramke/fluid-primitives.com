import * as asyncList from '@zag-js/async-list';
import type { CollectionItem } from '@zag-js/collection';
import { ListCollection } from '@zag-js/collection';
import type { InputValueChangeDetails } from '@zag-js/combobox';
import { createTemplateInstance, Machine, mountControlled } from 'fluid-primitives';
import { Combobox } from 'fluid-primitives/combobox';

interface CityResult extends CollectionItem {
    value: string;
    title: string;
    description?: string;
}

function debounce<Args extends unknown[]>(fn: (...args: Args) => void, delayMs: number) {
    let timeoutId: ReturnType<typeof setTimeout> | undefined;
    return (...args: Args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delayMs);
    };
}

mountControlled('combobox', 'async-search', ({ props, controlled }) => {
    const searchUrl = props.searchUrl as string;
    let insertedItems: HTMLElement[] = [];
    let combobox: Combobox;

    function updateItems(items: CityResult[]) {
        const contentEl = combobox.getElement<HTMLElement>('content');
        const templateEl = combobox.getElement<HTMLTemplateElement>('item-template');
        if (!contentEl || !templateEl || !combobox.hydrator) return;

        insertedItems.forEach(el => el.remove());
        insertedItems = [];

        for (const item of items) {
            const instance = createTemplateInstance(templateEl);
            const itemEl = instance.getRootElement<HTMLElement>();
            if (!itemEl) continue;

            itemEl.dataset.value = item.value;
            combobox.hydrator.setRefAttributes(itemEl, 'item', item.value);

            const titleEl = instance.getElement<HTMLElement>('title');
            if (titleEl) titleEl.textContent = item.title;

            const descriptionEl = instance.getElement<HTMLElement>('description');
            if (item.description) {
                if (descriptionEl) descriptionEl.textContent = item.description;
            } else {
                descriptionEl?.remove();
            }

            contentEl.appendChild(instance.toFragment());
            insertedItems.push(itemEl);
        }

        combobox.updateProps({
            collection: new ListCollection<CityResult>({
                items,
                itemToValue: item => item.value,
                itemToString: item => item.title,
            }),
        });
    }

    // Shown instead of the item list, mirroring the ark-ui async-list example: loading and error
    // take priority over stale results (async-list keeps the previous `items` around during a
    // refetch, so without this the old list and a "Searching…" message would show at once).
    function updateStatus(api: asyncList.Api<CityResult, unknown>, hasResults: boolean) {
        const contentEl = combobox.getElement<HTMLElement>('content');
        const statusEl = contentEl?.querySelector<HTMLElement>('[data-status]');
        const spinnerEl = contentEl?.querySelector<HTMLElement>('[data-status-spinner]');
        const textEl = contentEl?.querySelector<HTMLElement>('[data-status-text]');
        if (!statusEl || !spinnerEl || !textEl) return;

        statusEl.hidden = hasResults;
        if (hasResults) return;

        spinnerEl.hidden = !api.loading;

        if (api.loading) {
            textEl.textContent = 'Searching…';
        } else if (api.error) {
            textEl.textContent = 'Something went wrong. Please try again.';
        } else {
            textEl.textContent = api.filterText.trim()
                ? 'No results found'
                : 'Start typing to search…';
        }
    }

    const list = new Machine(asyncList.machine, {
        load: async ({ signal, filterText }) => {
            if (!filterText.trim()) return { items: [] as CityResult[] };

            // POST instead of appending `q` as a GET param: f:uri.action's cHash is computed from
            // the arguments known at build time, so a GET param added afterward would invalidate
            // it. A POST body sidesteps cHash entirely (it only governs the cacheable query string).
            const body = new URLSearchParams();
            body.set('tx_docs_docs[q]', filterText);

            const response = await fetch(searchUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: body.toString(),
                signal,
            });
            if (!response.ok) {
                throw new Error(`City search failed with status ${response.status}`);
            }

            return { items: (await response.json()) as CityResult[] };
        },
    } satisfies asyncList.Props<CityResult, unknown>);

    list.subscribe(service => {
        const api = asyncList.connect<CityResult, unknown>(service);
        const hasResults = !api.loading && !api.error && !api.empty;
        updateItems(hasResults ? api.items : []);
        updateStatus(api, hasResults);
    });

    const debouncedSearch = debounce((query: string) => {
        asyncList.connect<CityResult, unknown>(list.service).setFilterText(query);
    }, 300);

    combobox = new Combobox({
        ...props,
        controlled,
        onInputValueChange: (details: InputValueChangeDetails) => {
            if (details.reason === 'input-change') debouncedSearch(details.inputValue);
        },
    });

    list.start();
    combobox.init();
    return combobox;
});
