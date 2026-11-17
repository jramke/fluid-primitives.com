import { ListCollection } from '@zag-js/collection';
import { debounce } from '@zag-js/utils';
import { mount, Template } from 'fluid-primitives';
import { Combobox } from 'fluid-primitives/combobox';

interface Suggestion {
    value: string;
}

const SUGGEST_DELAY_MS = 200;

mount('ui:combobox', 'search-suggestions', ({ props }) => {
    let request: AbortController | undefined;
    let inserted: Element[] = [];

    function showSuggestions(items: Suggestion[]) {
        const list = combobox.hydrator.query<HTMLElement>('list');
        if (!list) return;

        inserted.forEach(el => el.remove());
        inserted = items.map(({ value }) => {
            const item = new Template(combobox.hydrator, 'suggestionTemplate', { value });
            item.query<HTMLElement>('title')!.textContent = value;
            list.appendChild(item);
            return item.root;
        });

        combobox.updateProps({
            collection: new ListCollection<Suggestion>({
                items,
                itemToValue: item => item.value,
                itemToString: item => item.value,
            }),
        });
    }

    const suggest = debounce(async (text: string) => {
        request?.abort();
        request = new AbortController();

        const url = new URL(props.searchUrl as string, location.href);
        url.searchParams.set('q', text);
        try {
            const response = await fetch(url, { signal: request.signal });
            showSuggestions(response.ok ? ((await response.json()) as Suggestion[]) : []);
        } catch {
            // An aborted request belongs to text that changed since
        }
    }, SUGGEST_DELAY_MS);

    const combobox: Combobox = new Combobox({
        ...props,
        onInputValueChange: ({ inputValue, reason }) => {
            if (reason === 'input-change') suggest(inputValue);
        },
        // A picked suggestion is the search word: the input shows it, then the form submits it
        onValueChange: ({ value }) => {
            if (value.length === 0) return;
            const input = combobox.hydrator.query<HTMLInputElement>('input');
            setTimeout(() => input?.form?.requestSubmit());
        },
    });

    return combobox.init();
});
