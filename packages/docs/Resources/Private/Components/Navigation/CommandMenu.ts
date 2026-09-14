import type { CollectionItem } from '@zag-js/collection';
import { ListCollection } from '@zag-js/collection';
import type { InputValueChangeDetails, SelectionDetails } from '@zag-js/combobox';
import { createHotkeyStore } from '@zag-js/hotkeys';
import { debounce } from '@zag-js/utils';
import { getHydrationData, Template, uid } from 'fluid-primitives';
import { Combobox } from 'fluid-primitives/combobox';
import { Dialog } from 'fluid-primitives/dialog';
import { create, insertMultiple, search, type ZBSearch } from 'zbsearch';

interface SearchDoc {
    title: string;
    group: string;
    url: string;
    content: string;
}

interface SearchResultItem extends CollectionItem {
    value: string;
    title: string;
    group: string;
}

const schema = {
    title: 'string',
    group: 'string',
    url: 'string',
    content: 'string',
} as const;

const INPUT_DEBOUNCE_MS = 120;
const RESULT_LIMIT = 40;

function toResultItem(doc: SearchDoc): SearchResultItem {
    return { value: doc.url, title: doc.title, group: doc.group };
}

/**
 * Boots the cmd+k command menu once per page: fetches the whole search index up front (so the
 * search itself is entirely client-side/offline afterwards), indexes it with zbsearch, and wires the
 * Dialog + Combobox composition together with a `mod+K` hotkey (via `@zag-js/hotkeys`) that opens
 * it from anywhere on the page.
 */
export function initCommandMenu(navRootId: string): void {
    const dialogHydrationData = getHydrationData('dialog', `search-${navRootId}`);
    const comboboxHydrationData = getHydrationData('combobox', `search-combobox-${navRootId}`);
    if (!dialogHydrationData || !comboboxHydrationData) return;

    let allItems: SearchResultItem[] = [];
    let groupOrder: string[] = [];
    let db: ZBSearch<typeof schema> | null = null;
    let insertedGroups: HTMLElement[] = [];

    function setStatus(text: string) {
        const el = combobox.getElement<HTMLElement>('statusText');
        if (el) el.textContent = text;
    }

    // Renders one flat item list, grouped the same way the sidebar navigation groups pages -
    // groupSort is given the nav.yaml group order explicitly, so groups render in that order
    // regardless of whether `items` is the full, unfiltered index (browsable list) or a subset of
    // zbsearch search hits (relevance order kept within each group).
    function updateItems(items: SearchResultItem[]) {
        const contentEl = combobox.getElement<HTMLElement>('content');
        if (!contentEl || !combobox.hydrator) return;

        insertedGroups.forEach(el => el.remove());
        insertedGroups = [];

        const collection = new ListCollection<SearchResultItem>({
            items,
            itemToValue: item => item.value,
            itemToString: item => item.title,
            groupBy: item => item.group,
            groupSort: groupOrder,
        });

        for (const [groupName, groupItems] of collection.group()) {
            const group = new Template(combobox.hydrator, 'groupTemplate');
            group.root.dataset.id = uid();

            const labelEl = group.getElement<HTMLElement>('group-label');
            if (labelEl) labelEl.textContent = groupName;

            for (const { value, title } of groupItems) {
                const item = new Template(combobox.hydrator, 'itemTemplate', { value });
                const titleEl = item.getElement<HTMLElement>('title');
                if (titleEl) titleEl.textContent = title;
                group.root.appendChild(item);
            }

            contentEl.appendChild(group);
            insertedGroups.push(group.root);
        }

        combobox.updateProps({ collection });

        if (items.length === 0) {
            setStatus('No results found.');
        }
    }

    async function runSearch(filterText: string) {
        const query = filterText.trim();

        if (query === '' || !db) {
            updateItems(allItems);
            return;
        }

        const results = await search(db, {
            term: query,
            properties: ['title', 'content'],
            boost: { title: 2 },
            limit: RESULT_LIMIT,
        });

        updateItems(results.hits.map(hit => toResultItem(hit.document)));
    }

    const runSearchDebounced = debounce(runSearch, INPUT_DEBOUNCE_MS);

    const combobox = new Combobox({
        ...comboboxHydrationData.props,
        controlled: true,
        open: true,
        onOpenChange: () => {},
        onInputValueChange: (details: InputValueChangeDetails) => {
            if (details.reason === 'input-change') runSearchDebounced(details.inputValue);
        },
        onSelect: (details: SelectionDetails) => {
            if (details.itemValue) window.location.href = details.itemValue;
        },
    });

    const dialog = new Dialog({
        ...dialogHydrationData.props,
        onOpenChange: details => {
            if (details.open) {
                requestAnimationFrame(() => {
                    combobox.getElement<HTMLInputElement>('input')?.focus();
                });
            } else {
                combobox.api.setInputValue('');
                if (db) updateItems(allItems);
            }
        },
    });

    combobox.init();
    dialog.init();

    const triggerEl = dialog.getElement<HTMLButtonElement>('trigger');
    const searchIndexUrl = triggerEl?.dataset.searchIndexUrl;
    if (!searchIndexUrl) return;

    fetch(searchIndexUrl)
        .then(response => (response.ok ? (response.json() as Promise<SearchDoc[]>) : []))
        .then(async docs => {
            allItems = docs.map(toResultItem);
            groupOrder = [...new Set(docs.map(doc => doc.group))];

            db = create({ schema });
            await insertMultiple(db, docs);

            runSearch(combobox.api.inputValue);
        })
        .catch(() => {
            setStatus('Search index could not be loaded.');
        });

    const hotkeys = createHotkeyStore();
    hotkeys.register({
        id: 'open-command-menu',
        hotkey: 'mod+K',
        action: () => dialog.api.setOpen(true),
        options: { preventDefault: true },
    });
    hotkeys.init({ target: document });
}
