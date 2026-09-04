import { mount } from 'fluid-primitives';
import { Combobox } from 'fluid-primitives/combobox';

mount('combobox', ({ props }) => {
	const combobox = new Combobox(props);
	combobox.init();
	return combobox;
});
