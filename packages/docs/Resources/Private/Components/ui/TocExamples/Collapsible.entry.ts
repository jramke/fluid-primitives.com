import { mount, mountAll } from 'fluid-primitives';
import { Toc } from 'fluid-primitives/toc';

mountAll('ui:tocExamples.collapsible', ({ props, createHydrator }) => {
    const hydrator = createHydrator();
    const ring = hydrator.query('ring');
    const number = hydrator.query('number');
    const label = hydrator.query('label');

    mount('ui:toc', `${props.id}-toc`, ({ props }) => {
        const toc = new Toc({
            ...props,
            onActiveChange: ({ activeIds }) => {
                const index = toc.api.items.findIndex(item => item.value === activeIds[0]);

                ring?.style.setProperty('--progress', String((index + 1) / toc.api.items.length));
                if (number) number.textContent = String(index + 1);
                if (label)
                    label.textContent =
                        toc.hydrator.queryAll('link')[index]?.textContent?.trim() ?? '';
            },
        });
        return toc.init();
    });
});
