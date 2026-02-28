import { mount } from 'fluid-primitives';
import { Field } from 'fluid-primitives/field';

mount('field', ({ props }) => {
	// @ts-expect-error
	const field = new Field(props);
	field.init();
	return field;
});
