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

// The item list is folded into the same value the status placeholder is driven by, rather than
// updated separately and immediately from the raw subscribe callback - otherwise the old items
// would be removed the instant a new search starts (api.loading flips true) while the "Searching…"
// placeholder stays hidden for its own showDelayMs, leaving a visible gap with nothing rendered
// in between. Routing both through one DelayedIndicator means the old items only ever disappear
// at the exact moment something else is ready to take their place - fresh results immediately, or
// the placeholder once it's actually shown.
type SearchState =
    | { status: 'loading' }
    | { status: 'error' }
    | { status: 'empty' }
    | { status: 'idle' }
    | { status: 'results'; items: CityResult[] };

function getSearchState(api: AsyncListApi<CityResult, unknown>): SearchState {
    const hasResults = !api.loading && !api.error && !api.empty;
    if (hasResults) return { status: 'results', items: api.items };
    if (api.loading) return { status: 'loading' };
    if (api.error) return { status: 'error' };
    return api.filterText.trim() ? { status: 'empty' } : { status: 'idle' };
}

mount('ui:combobox', 'async-search', ({ props }) => {
    const searchUrl = props.searchUrl as string;
    let insertedItems: HTMLElement[] = [];
    let combobox: Combobox;

    function updateItems(items: CityResult[]) {
        const contentEl = combobox.getElement<HTMLElement>('content');
        if (!contentEl || !combobox.hydrator) return;

        insertedItems.forEach(el => el.remove());
        insertedItems = [];

        const collection = new ListCollection<CityResult>({
            items,
            itemToValue: item => item.value,
            itemToString: item => item.title,
        });

        for (const { value, title, description } of collection) {
            const instance = new Template(combobox.hydrator, 'itemTemplate', { value });

            const titleEl = instance.getElement<HTMLElement>('title');
            if (titleEl) titleEl.textContent = title;

            const descriptionEl = instance.getElement<HTMLElement>('description');
            if (description) {
                if (descriptionEl) descriptionEl.textContent = description;
            } else {
                descriptionEl?.remove();
            }

            contentEl.appendChild(instance);
            insertedItems.push(instance.root);
        }

        combobox.updateProps({ collection });
    }

    // Renders both the item list and the status placeholder from one incoming state, so the two
    // are always in sync - old items and the old status text/spinner only ever change together,
    // at the moment DelayedIndicator decides something new is actually ready to be shown.
    const searchState = new DelayedIndicator<SearchState>({
        isTransient: s => s.status === 'loading',
        onChange: state => {
            const spinnerEl = combobox.getElement<HTMLElement>('statusSpinner');
            const textEl = combobox.getElement<HTMLElement>('statusText');
            if (!spinnerEl || !textEl) return;

            const hasResults = state.status === 'results';
            updateItems(hasResults ? state.items : []);
            if (hasResults) return;

            spinnerEl.toggleAttribute('hidden', state.status !== 'loading');
            textEl.textContent = STATUS_TEXT[state.status];
        },
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
        searchState.set(getSearchState(api));
    });

    // Debounced here rather than inside AsyncList itself - a plain wrap of setFilterText, the
    // same way you'd debounce any other callback.
    const setFilterTextDebounced = debounce(
        (filterText: string) => list.setFilterText(filterText),
        SEARCH_DEBOUNCE_MS
    );

    combobox = new Combobox({
        ...props,
        onInputValueChange: (details: InputValueChangeDetails) => {
            if (details.reason === 'input-change') setFilterTextDebounced(details.inputValue);
        },
    });

    list.init();
    combobox.init();
    return combobox;
});
