import { mount } from 'fluid-primitives';
import { CheckboxGroup } from 'fluid-primitives/checkbox-group';

mount('checkbox-group', ({ props }) => {
	const checkboxGroup = new CheckboxGroup(props);
	checkboxGroup.init();
	return checkboxGroup;
});
