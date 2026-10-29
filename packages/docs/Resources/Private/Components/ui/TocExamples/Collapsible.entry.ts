import { mount } from 'fluid-primitives';
import { Toc } from 'fluid-primitives/toc';

mount('ui:toc', 'collapsible-toc', ({ props }) => {
    const toc = new Toc({
        ...props,
        onActiveChange: ({ activeItems }) => {
            const root = toc.hydrator.query('root');
            const index = toc.api.items.findIndex(item => item.value === activeItems[0]?.value);
            const link = root?.querySelector(
                `[data-toc-link][data-value="${activeItems[0]?.value}"]`
            );

            root?.style.setProperty('--progress', String((index + 1) / toc.api.items.length));
            root?.querySelector('[data-number]')?.replaceChildren(String(index + 1));
            root?.querySelector('[data-label]')?.replaceChildren(link?.textContent?.trim() ?? '');
        },
    });
    return toc.init();
});
