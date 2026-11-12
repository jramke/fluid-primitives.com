import { extbase, mount, Template } from 'fluid-primitives';
import { Pagination } from 'fluid-primitives/pagination';

interface ArchiveEvent {
    title: string;
    city: string;
}

mount('ui:pagination', 'event-archive', ({ props }) => {
    const pagination: Pagination = new Pagination({
        ...props,
        loadPage: async ({ page, fetchUrl, signal }) => {
            const response = await extbase.post(fetchUrl, { currentPage: page }, { signal });
            const { events } = (await response.json()) as { events: ArchiveEvent[] };

            pagination.hydrator.query('events')!.replaceChildren(
                ...events.map(({ title, city }) => {
                    const row = new Template(pagination.hydrator, 'eventTemplate');
                    row.query('title')!.textContent = title;
                    row.query('city')!.textContent = city;
                    return row;
                })
            );
        },
    });
    return pagination.init();
});
