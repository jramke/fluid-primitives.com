// Loads a list into its region instead of reloading the page: the pages of its pagination and the results of
// its filter form. The template marks the region with data-region="name" and the form with
// data-region-form="name", data-region-live submits the form on every change. Without JavaScript the links and
// the form load the page as before, and the server renders the same list.
const HYDRATION_DATA = 'script[data-fluid-primitives-hydration-data]';
const TYPING_DELAY_MS = 400;

const announcer = document.createElement('p');
announcer.setAttribute('role', 'status');
announcer.className = 'sr-only';
document.body.append(announcer);

let loading: AbortController | undefined;

async function load(name: string, url: string, push: boolean): Promise<void> {
    const region = document.querySelector<HTMLElement>(`[data-region="${name}"]`);
    if (!region) return location.assign(url);

    loading?.abort();
    const controller = new AbortController();
    loading = controller;
    region.setAttribute('aria-busy', 'true');

    try {
        const response = await fetch(url, { signal: controller.signal });
        const page = new DOMParser().parseFromString(await response.text(), 'text/html');
        const next = page.querySelector(`[data-region="${name}"]`);
        if (!next) return location.assign(url);

        // The hydration data of the new components, the pagination, is a block of the new page. Added before the
        // markup, mountAll() mounts them when the markup arrives.
        document.head.append(...page.querySelectorAll(HYDRATION_DATA));
        region.replaceChildren(...next.childNodes);

        if (push) history.pushState({ region: name }, '', url);
        announcer.textContent =
            region.querySelector('[data-region-status]')?.textContent?.trim() ?? '';
    } catch (error) {
        if (!controller.signal.aborted) location.assign(url);
    } finally {
        if (loading === controller) region.removeAttribute('aria-busy');
    }
}

function formUrl(form: HTMLFormElement): string {
    const url = new URL(form.action);
    for (const [key, value] of new FormData(form)) {
        // Empty filters stay out of the URL
        if (typeof value === 'string' && value !== '') url.searchParams.append(key, value);
    }
    return url.toString();
}

document.addEventListener('click', event => {
    const link =
        event.target instanceof Element ? event.target.closest<HTMLAnchorElement>('a[href]') : null;
    const region = link?.closest<HTMLElement>('[data-region]');
    const isPaginationLink = link?.closest('[data-pagination-root]');
    if (!link || !region || !isPaginationLink || event.metaKey || event.ctrlKey || event.shiftKey)
        return;

    event.preventDefault();
    load(region.dataset.region!, link.href, true).then(() =>
        region.scrollIntoView({ behavior: 'smooth', block: 'start' })
    );
});

document.addEventListener('submit', event => {
    const form = event.target instanceof HTMLFormElement ? event.target : null;
    const name = form?.dataset.regionForm;
    if (!form || !name) return;

    event.preventDefault();
    load(name, formUrl(form), true);
});

let typing: ReturnType<typeof setTimeout> | undefined;
document.addEventListener('change', event => {
    const form = event.target instanceof Element ? event.target.closest('form') : null;
    if (form?.hasAttribute('data-region-live')) form.requestSubmit();
});
document.addEventListener('input', event => {
    const target = event.target;
    const form =
        target instanceof HTMLInputElement && target.type === 'search' ? target.form : null;
    if (!form?.hasAttribute('data-region-live')) return;

    clearTimeout(typing);
    typing = setTimeout(() => form.requestSubmit(), TYPING_DELAY_MS);
});

window.addEventListener('popstate', event => {
    const name = (event.state as { region?: string } | null)?.region;
    if (name) load(name, location.href, false);
});

// The first entry of the history needs the region too, so back from a loaded page restores it
const firstRegion = document.querySelector<HTMLElement>('[data-region]');
if (firstRegion) history.replaceState({ region: firstRegion.dataset.region }, '', location.href);
