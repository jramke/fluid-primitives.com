import { mount } from 'fluid-primitives';
import { Collapsible } from 'fluid-primitives/collapsible';

mount('collapsible', ({ props }) => {
	const collapsible = new Collapsible(props);
	collapsible.init();
	return collapsible;
});
