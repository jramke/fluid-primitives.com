import { mount } from 'fluid-primitives';
import { Select } from 'fluid-primitives/select';

mount('select', ({ props }) => {
	// @ts-expect-error
	const select = new Select(props);
	select.init();
	return select;
});
