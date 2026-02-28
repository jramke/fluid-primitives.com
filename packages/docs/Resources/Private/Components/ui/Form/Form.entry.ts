import { mount } from 'fluid-primitives';
import { Form } from 'fluid-primitives/form';

mount('form', ({ props }) => {
	const form = new Form(props);
	form.init();
	return form;
});
