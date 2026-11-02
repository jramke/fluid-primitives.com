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

interface SectionDoc {
    title: string;
    page: string;
    trail: string[];
    group: string;
    url: string;
}

interface SearchIndex {
    pages: SearchDoc[];
    sections: SectionDoc[];
}

interface SearchResultItem extends CollectionItem {
    value: string;
    title: string;
    group: string;
    trail?: string[];
}

const schema = {
    title: 'string',
    group: 'string',
    url: 'string',
    content: 'string',
} as const;

const sectionSchema = {
    title: 'string',
    page: 'string',
    trail: 'string[]',
    group: 'string',
    url: 'string',
} as const;

const INPUT_DEBOUNCE_MS = 75;
const RESULT_LIMIT = 40;
const MENTIONS_GROUP = 'Mentioned in';
const TRAILING_GROUPS = ['Sections', 'API Reference', MENTIONS_GROUP];

function toResultItem(doc: SearchDoc): SearchResultItem {
    return { value: doc.url, title: doc.title, group: doc.group };
}

function sectionToResultItem(doc: SectionDoc): SearchResultItem {
    return { value: doc.url, title: doc.trail.join(' > '), group: doc.group, trail: doc.trail };
}

class CommandMenu {
    private searchIndexUrl: string;
    private rootId: string;
    private combobox: Combobox;
    private dialog: Dialog;
    private db: ZBSearch<typeof schema> | null = null;
    private sectionDb: ZBSearch<typeof sectionSchema> | null = null;
    private allItems: SearchResultItem[] = [];
    private insertedGroups: HTMLElement[] = [];

    constructor(rootId: string, searchIndexUrl: string) {
        this.rootId = rootId;
        this.searchIndexUrl = searchIndexUrl;

        this.combobox = this.initCombobox();
        this.dialog = this.initDialog();

        fetch(this.searchIndexUrl)
            .then(response =>
                response.ok
                    ? (response.json() as Promise<SearchIndex>)
                    : { pages: [], sections: [] }
            )
            .then(async ({ pages, sections }) => {
                this.allItems = pages.map(toResultItem);

                this.sectionDb = create({ schema: sectionSchema });
                await insertMultiple(this.sectionDb, sections);
                this.db = create({ schema });
                await insertMultiple(this.db, pages);

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
        const combobox = mount('ui:combobox', `combobox-${this.rootId}`, ({ props }) => {
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
                    // a page of this site only: the value comes from a JSON response, and `javascript:` would run
                    const url = new URL(details.itemValue, window.location.origin);
                    if (url.origin !== window.location.origin) return;
                    window.location.href = url.href;
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
        const dialog = mount('primitives:dialog', `dialog-${this.rootId}`, ({ props }) => {
            const dialog = new Dialog({
                ...props,
                onOpenChange: details => {
                    if (details.open) {
                        requestAnimationFrame(() => {
                            this.combobox.hydrator.query<HTMLInputElement>('input')?.focus();
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
        const el = this.combobox.hydrator.query<HTMLElement>('statusText');
        if (el) el.textContent = text;
    }

    updateItems(items: SearchResultItem[]) {
        const listEl = this.combobox.hydrator.query<HTMLElement>('list');
        if (!listEl) return;

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

            const labelEl = group.query<HTMLElement>('group-label');
            if (labelEl) labelEl.textContent = groupName;

            for (const { value, title, trail } of groupItems) {
                const item = new Template(this.combobox.hydrator, 'itemTemplate', { value });
                const titleEl = item.query<HTMLElement>('title');
                titleEl?.replaceChildren(
                    ...(trail ?? [title]).flatMap((part, i) =>
                        i === 0
                            ? [part]
                            : [new Template(this.combobox.hydrator, 'separatorTemplate').root, part]
                    )
                );
                group.root.appendChild(item);
            }

            listEl.appendChild(group);
            this.insertedGroups.push(group.root);
        }

        this.combobox.updateProps({ collection });

        if (items.length === 0) {
            this.setStatus('No results found.');
        }
    }

    async runSearch(filterText: string) {
        const query = filterText.trim();

        if (query === '' || !this.db || !this.sectionDb) {
            this.updateItems(this.allItems);
            return;
        }

        const [titles, contents, sections] = await Promise.all([
            search(this.db, { term: query, properties: ['title'], limit: RESULT_LIMIT }),
            search(this.db, { term: query, properties: ['content'], limit: RESULT_LIMIT }),
            search(this.sectionDb, {
                term: query,
                properties: ['title', 'page'],
                boost: { title: 2 },
                limit: RESULT_LIMIT,
            }),
        ]);

        // A page that only mentions the term ranks below every heading that names it. It can't keep its nav group
        // there: groups render in the order they first appear, and a second "Components" would merge into the first.
        const pageItems = titles.hits.map(hit => toResultItem(hit.document));
        const titleUrls = new Set(pageItems.map(item => item.value));
        const trailingItems = [
            ...sections.hits.map(hit => sectionToResultItem(hit.document)),
            ...contents.hits
                .filter(hit => !titleUrls.has(hit.document.url))
                .map(hit => ({ ...toResultItem(hit.document), group: MENTIONS_GROUP })),
        ];
        this.updateItems([
            ...pageItems,
            ...TRAILING_GROUPS.flatMap(group => trailingItems.filter(item => item.group === group)),
        ]);
    }

    debouncedSearch = debounce(this.runSearch.bind(this), INPUT_DEBOUNCE_MS);
}

mountAll('ui:commandMenu', ({ props }) => {
    new CommandMenu(props.id, props.searchUrl);
});
