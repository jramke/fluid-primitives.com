import { mount } from 'fluid-primitives';
import { RadioGroup } from 'fluid-primitives/radio-group';

mount('radio-group', ({ props }) => {
	const radioGroup = new RadioGroup(props);
	radioGroup.init();
	return radioGroup;
});
