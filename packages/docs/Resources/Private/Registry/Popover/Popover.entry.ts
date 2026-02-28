import { mount } from 'fluid-primitives';
import { Popover } from 'fluid-primitives/popover';

mount('popover', ({ props }) => {
	const popover = new Popover(props);
	popover.init();
	return popover;
});
