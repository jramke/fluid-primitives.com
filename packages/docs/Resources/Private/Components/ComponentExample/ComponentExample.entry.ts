import { getHydrationData, mount } from 'fluid-primitives';
import { Tabs } from 'fluid-primitives/tabs';

mount('component-example', ({ props, createHydrator }) => {
	const hydrator = createHydrator();
	const tabActions = hydrator.getElements<HTMLDivElement>('tab-actions');

	const tabsProps = getHydrationData('tabs', `${props.id}-tabs`)?.props;
	if (!tabsProps) return;

	tabActions.forEach(el => {
		const value = el.dataset.value;
		if (!value) {
			console.warn('Tab action element is missing data-value attribute', el);
		}
	});

	const tabs = new Tabs({
		...tabsProps,
		onValueChange: ({ value }) => {
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
