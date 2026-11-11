import { extbase, mount, Template } from 'fluid-primitives';
import { Pagination } from 'fluid-primitives/pagination';

interface ArchiveEvent {
    title: string;
    city: string;
}

mount('ui:pagination', 'event-archive', ({ props }) => {
    const pagination: Pagination = new Pagination({
        ...props,
        onPageChange: ({ page }) => {
            // The history entry remembers its page for back and forward
            history.replaceState({ page }, '');
            showEvents(page);
        },
    });
    pagination.init();
    history.replaceState({ page: pagination.api.page }, '');

    const rootEl = pagination.hydrator.query('root')!;
    const eventsEl = pagination.hydrator.query('events')!;

    async function showEvents(page: number) {
        const response = await extbase.post(eventsEl.dataset.url!, { currentPage: page });
        const { events } = (await response.json()) as { events: ArchiveEvent[] };
        // A click on another page while this one loads wins
        if (page !== pagination.api.page) return;

        eventsEl.replaceChildren(
            ...events.map(({ title, city }) => {
                const row = new Template(pagination.hydrator, 'eventTemplate');
                row.query('title')!.textContent = title;
                row.query('city')!.textContent = city;
                return row;
            })
        );
    }

    // A click on a link adds its URL to the history instead of loading it. A click with a modifier
    // key opens the link in a new tab and leaves this page as it is.
    rootEl.addEventListener(
        'click',
        event => {
            const link = (event.target as Element).closest('a[href]');
            if (!link) return;
            if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey) {
                event.stopPropagation();
                return;
            }
            event.preventDefault();
            history.pushState(null, '', link.getAttribute('href'));
        },
        { capture: true }
    );

    // Back and forward only change the URL: show the page of the history entry
    window.addEventListener('popstate', event => {
        if (typeof event.state?.page === 'number') pagination.api.setPage(event.state.page);
    });

    return pagination;
});
