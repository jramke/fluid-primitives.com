import { getHydrationData, mountAll } from 'fluid-primitives';
import { Collapsible } from 'fluid-primitives/collapsible';
import { Tabs } from 'fluid-primitives/tabs';

mountAll('componentExample', ({ props, createHydrator }) => {
    const hydrator = createHydrator();
    const tabActions = hydrator.getElements<HTMLDivElement>('tab-actions');

    const tabsProps = getHydrationData('tabs', `${props.id}-tabs`)?.props;
    if (!tabsProps) return;

    const collapsibleProps = getHydrationData('collapsible', `${props.id}-collapsible`)?.props;
    if (!collapsibleProps) return;

    tabActions.forEach(el => {
        const value = el.dataset.value;
        if (!value) {
            console.warn('Tab action element is missing data-value attribute', el);
        }
    });

    const collapsible = new Collapsible(collapsibleProps);
    collapsible.init();

    const tabs = new Tabs({
        ...tabsProps,
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
    });
    tabs.init();
});
