import { mount } from 'fluid-primitives';
import { Tabs } from 'fluid-primitives/tabs';

mount('tabs', ({ props }) => {
	const tabs = new Tabs(props);
	tabs.init();
	return tabs;
});
