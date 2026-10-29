// The summary in the trigger follows the data-active attribute the Toc sets on its items, so it needs no
// handle on the Toc instance.
for (const root of document.querySelectorAll<HTMLElement>('[data-summary]')) {
    const items = [...root.querySelectorAll<HTMLElement>('[data-toc-item]')];
    const label = root.querySelector<HTMLElement>('[data-summary-label]');
    const number = root.querySelector<HTMLElement>('[data-summary-number]');
    const title = label?.textContent ?? '';

    const update = () => {
        const index = items.findIndex(item => item.hasAttribute('data-active'));
        root.style.setProperty('--progress', String((index + 1) / items.length));
        if (label)
            label.textContent = index === -1 ? title : (items[index].textContent?.trim() ?? title);
        if (number) number.textContent = index === -1 ? '-' : String(index + 1);
    };

    new MutationObserver(update).observe(root, {
        attributes: true,
        attributeFilter: ['data-active'],
        subtree: true,
    });
    update();
}
