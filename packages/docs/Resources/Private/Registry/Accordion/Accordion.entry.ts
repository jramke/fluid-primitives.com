import { mount } from 'fluid-primitives';
import { Accordion } from 'fluid-primitives/accordion';

mount('accordion', ({ props }) => {
	const accordion = new Accordion(props);
	accordion.init();
	return accordion;
});
