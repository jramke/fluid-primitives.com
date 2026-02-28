import { mount } from 'fluid-primitives';
import { Checkbox } from 'fluid-primitives/checkbox';

mount('checkbox', ({ props }) => {
	const checkbox = new Checkbox(props);
	checkbox.init();
	return checkbox;
});
