// The address bar of a demo frame follows the page in the frame: a new page, and a URL a script of the page
// changes, as the news filters do
document.querySelectorAll<HTMLElement>('[data-demo-frame]').forEach(frame => {
    const iframe = frame.querySelector('iframe');
    const url = frame.querySelector('[data-demo-frame-url]');
    const link = frame.querySelector<HTMLAnchorElement>('[data-demo-frame-link]');
    if (!iframe || !url || !link) return;

    const update = () => {
        const location = iframe.contentWindow?.location;
        if (!location || location.href === 'about:blank') return;
        url.textContent = location.pathname + location.search;
        link.href = location.href;
    };

    const follow = () => {
        update();
        // The Navigation API reports history.pushState(), the DOM types of TypeScript do not know it yet
        const frameWindow = iframe.contentWindow as (Window & { navigation?: EventTarget }) | null;
        frameWindow?.navigation?.addEventListener('currententrychange', update);
    };

    iframe.addEventListener('load', follow);
    // The frame can finish loading before this script runs
    if (iframe.contentDocument?.readyState === 'complete') follow();
});
