import type { Api as AsyncListApi } from '@zag-js/async-list';
import type { CollectionItem } from '@zag-js/collection';
import { ListCollection } from '@zag-js/collection';
import type { InputValueChangeDetails } from '@zag-js/combobox';
import { debounce } from '@zag-js/utils';
import {
    AsyncList,
    DelayedIndicator,
    extbase,
    mountControlled,
    Template,
    uid,
} from 'fluid-primitives';
import { Combobox } from 'fluid-primitives/combobox';

interface CityResult extends CollectionItem {
    value: string;
    title: string;
    description?: string;
}

const SEARCH_DEBOUNCE_MS = 300;

type SearchStatus = 'loading' | 'error' | 'empty' | 'idle' | 'results';

const STATUS_TEXT: Record<Exclude<SearchStatus, 'results'>, string> = {
    loading: 'Searching…',
    error: 'Something went wrong. Please try again.',
    empty: 'No results found',
    idle: 'Start typing to search…',
};

function getSearchStatus(
    api: AsyncListApi<CityResult, unknown>,
    hasResults: boolean
): SearchStatus {
    if (hasResults) return 'results';
    if (api.loading) return 'loading';
    if (api.error) return 'error';
    return api.filterText.trim() ? 'empty' : 'idle';
}

mountControlled('combobox', 'async-search-grouped', ({ props, controlled }) => {
    const searchUrl = props.searchUrl as string;
    let insertedGroups: HTMLElement[] = [];
    let combobox: Combobox;

    function updateItems(items: CityResult[]) {
        const contentEl = combobox.getElement<HTMLElement>('content');
        if (!contentEl || !combobox.hydrator) return;

        insertedGroups.forEach(el => el.remove());
        insertedGroups = [];

        // Grouping by country is baked into the collection itself (groupBy/groupSort), the same
        // way the static "With Item Groups" example groups server-side via groupByKey/groupSort -
        // group() then just reads back what the collection already grouped/sorted.
        const collection = new ListCollection<CityResult>({
            items,
            itemToValue: item => item.value,
            itemToString: item => item.title,
            groupBy: item => item.description || 'Other',
            groupSort: 'asc',
        });

        for (const [country, countryItems] of collection.group()) {
            const group = new Template(combobox.hydrator, 'group-template');
            // Only the value/identity discriminator Combobox's own render() needs to tell groups
            // apart - the label text and item rendering are unaffected by its exact value.
            group.root.dataset.id = uid();

            const labelEl = group.getElement<HTMLElement>('group-label');
            if (labelEl) labelEl.textContent = country;

            for (const { value, title } of countryItems) {
                const item = new Template(combobox.hydrator, 'item-template', { value });
                const titleEl = item.getElement<HTMLElement>('title');
                if (titleEl) titleEl.textContent = title;
                group.root.appendChild(item);
            }

            contentEl.appendChild(group);
            insertedGroups.push(group.root);
        }

        combobox.updateProps({ collection });
    }

    // Shown instead of the item list, mirroring the ark-ui async-list example: loading and error
    // take priority over stale results (async-list keeps the previous `items` around during a
    // refetch, so without this the old list and a "Searching…" message would show at once).
    // Driven only through the DelayedIndicator below, so the spinner and the text can never
    // disagree - a fast search never flashes either, and a slow one shows both together.
    function renderStatus(status: SearchStatus) {
        const contentEl = combobox.getElement<HTMLElement>('content');
        const statusEl = contentEl?.querySelector<HTMLElement>('[data-status]');
        const spinnerEl = contentEl?.querySelector<HTMLElement>('[data-status-spinner]');
        const textEl = contentEl?.querySelector<HTMLElement>('[data-status-text]');
        if (!statusEl || !spinnerEl || !textEl) return;

        statusEl.toggleAttribute('hidden', status === 'results');
        if (status === 'results') return;

        spinnerEl.toggleAttribute('hidden', status !== 'loading');
        textEl.textContent = STATUS_TEXT[status];
    }

    const status = new DelayedIndicator<SearchStatus>({
        isPending: s => s === 'loading',
        onChange: renderStatus,
    });

    const list = new AsyncList<CityResult>({
        load: async ({ signal, filterText }) => {
            if (!filterText.trim()) return { items: [] as CityResult[] };

            // post() namespaces { q: filterText } under searchUrl's own tx_docs_docs[...]
            // prefix automatically and sends it as a POST body, sidestepping the cHash mismatch
            // a GET query param appended after the fact would otherwise cause (f:uri.action's
            // cHash is computed from the arguments known at build time).
            const response = await extbase.post(searchUrl, { q: filterText }, { signal });
            if (!response.ok) {
                throw new Error(`City search failed with status ${response.status}`);
            }
            return { items: (await response.json()) as CityResult[] };
        },
    });

    list.subscribe(api => {
        const hasResults = !api.loading && !api.error && !api.empty;
        updateItems(hasResults ? api.items : []);
        status.set(getSearchStatus(api, hasResults));
    });

    // Debounced here rather than inside AsyncList itself - a plain wrap of setFilterText, the
    // same way you'd debounce any other callback.
    const setFilterTextDebounced = debounce(
        (filterText: string) => list.setFilterText(filterText),
        SEARCH_DEBOUNCE_MS
    );

    combobox = new Combobox({
        ...props,
        controlled,
        onInputValueChange: (details: InputValueChangeDetails) => {
            if (details.reason === 'input-change') setFilterTextDebounced(details.inputValue);
        },
    });

    list.init();
    combobox.init();
    return combobox;
});
