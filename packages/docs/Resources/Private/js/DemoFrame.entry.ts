// The address bar of a demo frame follows the page in the frame
document.querySelectorAll<HTMLElement>('[data-demo-frame]').forEach(frame => {
    const iframe = frame.querySelector('iframe');
    const url = frame.querySelector('[data-demo-frame-url]');
    const link = frame.querySelector<HTMLAnchorElement>('[data-demo-frame-link]');

    iframe?.addEventListener('load', () => {
        const location = iframe.contentWindow?.location;
        if (!location || !url || !link) return;
        url.textContent = location.pathname + location.search;
        link.href = location.href;
    });
});
