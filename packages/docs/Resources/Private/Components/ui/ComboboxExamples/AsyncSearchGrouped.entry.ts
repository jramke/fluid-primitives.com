import type { Api as AsyncListApi } from '@zag-js/async-list';
import type { CollectionItem } from '@zag-js/collection';
import { ListCollection } from '@zag-js/collection';
import type { InputValueChangeDetails } from '@zag-js/combobox';
import { debounce } from '@zag-js/utils';
import { AsyncList, DelayedIndicator, extbase, mount, Template } from 'fluid-primitives';
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

// The group/item list is folded into the same value the status placeholder is driven by, rather
// than updated separately and immediately from the raw subscribe callback - otherwise the old
// groups would be removed the instant a new search starts (api.isLoading flips true) while the
// "Searching…" placeholder stays hidden for its own showDelayMs, leaving a visible gap with
// nothing rendered in between. Routing both through one DelayedIndicator means the old groups
// only ever disappear at the exact moment something else is ready to take their place - fresh
// results immediately, or the placeholder once it's actually shown.
type SearchState =
    | { status: 'loading' }
    | { status: 'error' }
    | { status: 'empty' }
    | { status: 'idle' }
    | { status: 'results'; items: CityResult[] };

function getSearchState(api: AsyncListApi<CityResult>): SearchState {
    const hasResults = !api.isLoading && !api.error && !api.isEmpty;
    if (hasResults) return { status: 'results', items: api.items };
    if (api.isLoading) return { status: 'loading' };
    if (api.error) return { status: 'error' };
    return api.filter.trim() ? { status: 'empty' } : { status: 'idle' };
}

mount('ui:combobox', 'async-search-grouped', ({ props }) => {
    const searchUrl = props.searchUrl as string;
    let insertedGroups: HTMLElement[] = [];
    let combobox: Combobox;

    function updateItems(items: CityResult[]) {
        const listEl = combobox.hydrator.query<HTMLElement>('list');
        if (!listEl) return;

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
            // The group's own key doubles as its restamp value - stable and unique per group
            // (unlike a random uid()), and what Combobox's own render() needs to correctly link
            // each group's content/label pair via id/aria-labelledby (spreadPropsByValue('itemGroup', ...)).
            const group = new Template(combobox.hydrator, 'groupTemplate', { value: country });

            const labelEl = group.query<HTMLElement>('group-label');
            if (labelEl) labelEl.textContent = country;

            for (const { value, title } of countryItems) {
                const item = new Template(combobox.hydrator, 'itemTemplate', { value });
                const titleEl = item.query<HTMLElement>('title');
                if (titleEl) titleEl.textContent = title;
                group.root.appendChild(item);
            }

            listEl.appendChild(group);
            insertedGroups.push(group.root);
        }

        combobox.updateProps({ collection });
    }

    // Renders both the group/item list and the status placeholder from one incoming state, so
    // the two are always in sync - old groups and the old status text/spinner only ever change
    // together, at the moment DelayedIndicator decides something new is actually ready to be shown.
    const searchState = new DelayedIndicator<SearchState>({
        isTransient: s => s.status === 'loading',
        onChange: state => {
            const spinnerEl = combobox.hydrator.query<HTMLElement>('statusSpinner');
            const textEl = combobox.hydrator.query<HTMLElement>('statusText');
            if (!spinnerEl || !textEl) return;

            const hasResults = state.status === 'results';
            updateItems(hasResults ? state.items : []);
            if (hasResults) return;

            spinnerEl.toggleAttribute('hidden', state.status !== 'loading');
            textEl.textContent = STATUS_TEXT[state.status];
        },
    });

    const list = new AsyncList<CityResult>({
        load: async ({ signal, filter }) => {
            if (!filter.trim()) return { items: [] as CityResult[] };

            // post() namespaces { q: filter } under searchUrl's own tx_docs_docs[...]
            // prefix automatically and sends it as a POST body, sidestepping the cHash mismatch
            // a GET query param appended after the fact would otherwise cause (f:uri.action's
            // cHash is computed from the arguments known at build time).
            const response = await extbase.post(searchUrl, { q: filter }, { signal });
            return { items: (await response.json()) as CityResult[] };
        },
    });

    list.subscribe(api => {
        searchState.set(getSearchState(api));
    });

    // Debounced here rather than inside AsyncList itself - a plain wrap of setFilter, the
    // same way you'd debounce any other callback.
    const setFilterDebounced = debounce(
        (filter: string) => list.setFilter(filter),
        SEARCH_DEBOUNCE_MS
    );

    combobox = new Combobox({
        ...props,
        onInputValueChange: (details: InputValueChangeDetails) => {
            if (details.reason === 'input-change') setFilterDebounced(details.inputValue);
        },
    });

    list.init();
    combobox.init();
    return combobox;
});
