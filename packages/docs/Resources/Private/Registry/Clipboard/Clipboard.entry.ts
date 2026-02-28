import { mount } from 'fluid-primitives';
import { Clipboard } from 'fluid-primitives/clipboard';

mount('clipboard', ({ props }) => {
	const clipboard = new Clipboard(props);
	clipboard.init();
	return clipboard;
});
