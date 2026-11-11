import { extbase, mount, Template } from 'fluid-primitives';
import { Pagination } from 'fluid-primitives/pagination';

interface ArchiveEvent {
    title: string;
    city: string;
}

// The links stay real links: a crawler and a visitor without JavaScript load the page of a link.
// Here a click changes the page on the client and loads its events from a JSON endpoint.
mount('ui:pagination', 'event-archive', ({ props }) => {
    // The URL of every page, built by the server
    const pageUrls = props.pageUrls as Record<number, string>;
    let request: AbortController | undefined;
    let fromHistory = false;

    const pagination: Pagination = new Pagination({
        ...props,
        onPageChange: ({ page }) => {
            if (!fromHistory) history.pushState({ page }, '', pageUrls[page]);
            fromHistory = false;
            showEvents(page);
        },
    });

    async function showEvents(page: number) {
        request?.abort();
        request = new AbortController();
        try {
            const eventsEl = pagination.hydrator.query('events')!;
            const response = await extbase.post(
                eventsEl.dataset.url!,
                { page },
                { signal: request.signal }
            );
            if (!response.ok)
                throw new Error(`Loading page ${page} failed with ${response.status}`);
            const { events } = (await response.json()) as { events: ArchiveEvent[] };

            const rows = events.map(({ title, city }) => {
                const row = new Template(pagination.hydrator, 'eventTemplate');
                row.query('title')!.textContent = title;
                row.query('city')!.textContent = city;
                return row;
            });
            eventsEl.replaceChildren(...rows);
        } catch (error) {
            // A click on another page cancels the request of this one
            if ((error as Error).name !== 'AbortError') window.location.href = pageUrls[page];
        }
    }

    pagination.init();
    history.replaceState({ page: pagination.api.page }, '');

    // The pagination changes the page itself, the browser only opens a link in a new tab
    pagination.hydrator.query('root')?.addEventListener(
        'click',
        event => {
            if (!(event.target as Element).closest('a[href]')) return;
            if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey) {
                event.stopPropagation();
            } else {
                event.preventDefault();
            }
        },
        { capture: true }
    );

    window.addEventListener('popstate', event => {
        if (typeof event.state?.page !== 'number') return;
        fromHistory = true;
        pagination.api.setPage(event.state.page);
    });

    return pagination;
});
