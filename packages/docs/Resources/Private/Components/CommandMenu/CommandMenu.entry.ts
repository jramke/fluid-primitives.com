import type { CollectionItem } from '@zag-js/collection';
import { ListCollection } from '@zag-js/collection';
import type { InputValueChangeDetails, SelectionDetails } from '@zag-js/combobox';
import { createHotkeyStore } from '@zag-js/hotkeys';
import { debounce } from '@zag-js/utils';
import { mount, mountAll, Template } from 'fluid-primitives';
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

const INPUT_DEBOUNCE_MS = 75;
const RESULT_LIMIT = 40;

function toResultItem(doc: SearchDoc): SearchResultItem {
    return { value: doc.url, title: doc.title, group: doc.group };
}

class CommandMenu {
    private searchIndexUrl: string;
    private rootId: string;
    private combobox: Combobox;
    private dialog: Dialog;
    private db: ZBSearch<typeof schema> | null = null;
    private allItems: SearchResultItem[] = [];
    private insertedGroups: HTMLElement[] = [];

    constructor(rootId: string, searchIndexUrl: string) {
        this.rootId = rootId;
        this.searchIndexUrl = searchIndexUrl;

        this.combobox = this.initCombobox();
        this.dialog = this.initDialog();

        fetch(this.searchIndexUrl)
            .then(response => (response.ok ? (response.json() as Promise<SearchDoc[]>) : []))
            .then(async docs => {
                this.allItems = docs.map(toResultItem);

                this.db = create({ schema });
                await insertMultiple(this.db, docs);

                this.runSearch(this.combobox.api.inputValue);
            })
            .catch(() => {
                this.setStatus('Search index could not be loaded.');
            });

        const hotkeys = createHotkeyStore();
        hotkeys.register({
            id: 'open-command-menu',
            hotkey: 'mod+K',
            action: () => this.dialog.api.setOpen(true),
            options: { preventDefault: true },
        });
        hotkeys.init({ target: document });
    }

    initCombobox() {
        const combobox = mount('combobox', `combobox-${this.rootId}`, ({ props }) => {
            const combobox = new Combobox({
                ...props,
                open: true,
                positioning: {
                    gutter: 0,
                },
                inputBehavior: 'autohighlight',
                onInputValueChange: (details: InputValueChangeDetails) => {
                    if (details.reason === 'input-change') this.debouncedSearch(details.inputValue);
                },
                onSelect: (details: SelectionDetails) => {
                    if (!details.itemValue) return;
                    window.location.href = details.itemValue;
                    this.dialog.api.setOpen(false);
                },
            });
            combobox.init();
            return combobox;
        });

        if (!combobox) throw new Error('Combobox initialization failed');

        return combobox;
    }

    initDialog() {
        const dialog = mount('dialog', `dialog-${this.rootId}`, ({ props }) => {
            const dialog = new Dialog({
                ...props,
                onOpenChange: details => {
                    if (details.open) {
                        requestAnimationFrame(() => {
                            this.combobox.getElement<HTMLInputElement>('input')?.focus();
                        });
                    } else {
                        this.combobox.api.setInputValue('');
                        if (this.db) this.updateItems(this.allItems);
                    }
                },
            });
            dialog.init();
            return dialog;
        });

        if (!dialog) throw new Error('Dialog initialization failed');

        return dialog;
    }

    setStatus(text: string) {
        const el = this.combobox.getElement<HTMLElement>('statusText');
        if (el) el.textContent = text;
    }

    updateItems(items: SearchResultItem[]) {
        const contentEl = this.combobox.getElement<HTMLElement>('content');
        if (!contentEl || !this.combobox.hydrator) return;

        this.insertedGroups.forEach(el => el.remove());
        this.insertedGroups = [];

        const collection = new ListCollection<SearchResultItem>({
            items,
            itemToValue: item => item.value,
            itemToString: item => item.title,
            groupBy: item => item.group,
        });

        for (const [groupName, groupItems] of collection.group()) {
            // The group's own key doubles as its restamp value - stable and unique per group
            // (unlike a random uid()), and what Combobox's own render() needs to correctly link
            // each group's content/label pair via id/aria-labelledby (spreadPropsByValue('itemGroup', ...)).
            const group = new Template(this.combobox.hydrator, 'groupTemplate', {
                value: groupName,
            });

            const labelEl = group.getElement<HTMLElement>('group-label');
            if (labelEl) labelEl.textContent = groupName;

            for (const { value, title } of groupItems) {
                const item = new Template(this.combobox.hydrator, 'itemTemplate', { value });
                const titleEl = item.getElement<HTMLElement>('title');
                if (titleEl) titleEl.textContent = title;
                group.root.appendChild(item);
            }

            contentEl.appendChild(group);
            this.insertedGroups.push(group.root);
        }

        this.combobox.updateProps({ collection });

        if (items.length === 0) {
            this.setStatus('No results found.');
        }
    }

    async runSearch(filterText: string) {
        const query = filterText.trim();

        if (query === '' || !this.db) {
            this.updateItems(this.allItems);
            return;
        }

        const results = await search(this.db, {
            term: query,
            properties: ['title', 'content'],
            boost: { title: 2 },
            limit: RESULT_LIMIT,
        });

        this.updateItems(results.hits.map(hit => toResultItem(hit.document)));
    }

    debouncedSearch = debounce(this.runSearch.bind(this), INPUT_DEBOUNCE_MS);
}

mountAll('commandMenu', ({ props }) => {
    new CommandMenu(props.id, props.searchUrl);
});
