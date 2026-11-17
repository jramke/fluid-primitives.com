import { mount } from 'fluid-primitives';
import { DatePicker } from 'fluid-primitives/date-picker';
import { Input } from 'fluid-primitives/input';

const filters = document.querySelector<HTMLFormElement>('#news-filters');
const results = document.querySelector<HTMLElement>('#news-results');
const status = document.querySelector<HTMLElement>('#news-status');

// Loads the list of another URL into this page: the results of the filters, or a page of the pagination. The
// address bar follows, and back and forward load their URL the same way.
let loading: AbortController | undefined;

async function loadResults(url: string, { push = true } = {}) {
    if (!results) return location.assign(url);

    loading?.abort();
    const controller = new AbortController();
    loading = controller;
    results.setAttribute('aria-busy', 'true');

    try {
        const response = await fetch(url, { signal: controller.signal });
        const page = new DOMParser().parseFromString(await response.text(), 'text/html');
        const next = page.querySelector('#news-results');
        if (!next) return location.assign(url);

        // The new pagination brings its hydration data in a block of the new page. In the head next to the
        // markup, mountAll() mounts it.
        document.head.append(
            ...page.querySelectorAll('script[data-fluid-primitives-hydration-data]')
        );
        results.replaceChildren(...next.childNodes);

        if (push) history.pushState(null, '', url);
        if (status)
            status.textContent =
                results.querySelector('[data-news-count]')?.textContent?.trim() ?? '';
    } catch {
        if (!controller.signal.aborted) location.assign(url);
    } finally {
        if (loading === controller) results.removeAttribute('aria-busy');
    }
}

// The URL of the form as a GET submit builds it, without the empty filters
function submitFilters() {
    if (!filters) return;
    const url = new URL(filters.action);
    for (const [key, value] of new FormData(filters)) {
        if (typeof value === 'string' && value !== '') url.searchParams.append(key, value);
    }
    loadResults(url.toString());
}

filters?.addEventListener('submit', event => {
    event.preventDefault();
    submitFilters();
});

// The category Select changes its hidden select. The search submits while you type, see below.
filters?.addEventListener('change', event => {
    if ((event.target as HTMLInputElement).type !== 'search') submitFilters();
});

results?.addEventListener('click', event => {
    const link = (event.target as Element).closest<HTMLAnchorElement>(
        '[data-pagination-root] a[href]'
    );
    if (!link || event.metaKey || event.ctrlKey || event.shiftKey) return;

    event.preventDefault();
    loadResults(link.href).then(() =>
        results.scrollIntoView({ behavior: 'smooth', block: 'start' })
    );
});

window.addEventListener('popstate', () => loadResults(location.href, { push: false }));

// The search of EXT:news takes the start and the end of the period as two arguments. A single day is a period
// that ends on the day it starts.
mount('ui:datePicker', 'news-date', ({ props }) => {
    const datePicker: DatePicker = new DatePicker({
        ...props,
        onValueChange: ({ value }) => {
            const start = datePicker.hydrator.query<HTMLInputElement>('periodStart');
            const end = datePicker.hydrator.query<HTMLInputElement>('periodEnd');
            const text = datePicker.hydrator.query<HTMLElement>('periodText');
            if (!start || !end || !text) return;

            // ISO dates, whatever format the site shows
            const [first, last = first] = value;
            start.value = first?.toString() ?? '';
            end.value = last?.toString() ?? '';

            // The format of the picker knows its locale and time zone
            const dates =
                first && last && first.compare(last) !== 0 ? [first, last] : value.slice(0, 1);
            text.textContent = dates.length
                ? dates
                      .map(date => datePicker.api.format(date, { dateStyle: 'medium' }))
                      .join(' – ')
                : 'Any date';
            text.toggleAttribute('data-empty', !dates.length);

            submitFilters();
        },
    });
    return datePicker.init();
});

mount('ui:input', 'news-search', ({ props }) => {
    const search = new Input(props).init();
    const input = search.hydrator.query<HTMLInputElement>('input');
    let typing: ReturnType<typeof setTimeout> | undefined;

    input?.addEventListener('input', () => {
        clearTimeout(typing);
        typing = setTimeout(submitFilters, 400);
    });

    // An input has no clear button of its own
    search.hydrator.query('clear')?.addEventListener('click', () => {
        if (!input) return;
        input.value = '';
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.focus();
    });

    return search;
});
