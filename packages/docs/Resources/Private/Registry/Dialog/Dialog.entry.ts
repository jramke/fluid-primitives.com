import { mount } from 'fluid-primitives';
import { Dialog } from 'fluid-primitives/dialog';

mount('dialog', ({ props }) => {
	const dialog = new Dialog(props);
	dialog.init();
	return dialog;
});
