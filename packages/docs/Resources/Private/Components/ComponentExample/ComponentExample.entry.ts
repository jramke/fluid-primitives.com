import { mount, mountAll } from 'fluid-primitives';
import { Collapsible } from 'fluid-primitives/collapsible';
import { Tabs } from 'fluid-primitives/tabs';

mountAll('ui:componentExample', ({ props, createHydrator }) => {
    const hydrator = createHydrator();
    const tabActions = hydrator.queryAll<HTMLDivElement>('tab-actions');

    tabActions.forEach(el => {
        const value = el.dataset.value;
        if (!value) {
            console.warn('Tab action element is missing data-value attribute', el);
        }
    });

    const collapsible = mount('ui:collapsible', `${props.id}-collapsible`, ({ props }) =>
        new Collapsible(props).init()
    );
    if (!collapsible) return;

    mount('ui:tabs', `${props.id}-tabs`, ({ props }) =>
        new Tabs({
            ...props,
            onValueChange: ({ value }) => {
                if (!collapsible.api.open) {
                    collapsible.api.setOpen(true);
                }
                tabActions.forEach(el => {
                    if (el.dataset.value === value) {
                        el.removeAttribute('hidden');
                    } else {
                        el.setAttribute('hidden', 'true');
                    }
                });
            },
        }).init()
    );
});
