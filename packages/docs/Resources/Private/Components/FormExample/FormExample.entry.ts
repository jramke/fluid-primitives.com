import { getHydrationData, mount } from 'fluid-primitives';
import { Form, ValidationError } from 'fluid-primitives/form';

mount('form-example', () => {
	const data = getHydrationData('form', 'example-form');
	if (!data) return;

	const form = new Form({
		...data.props,
		onSubmit: async ({ formData }) => {
			await new Promise(resolve => setTimeout(resolve, 800));

			if (formData.get('homepage') === 'https://example.com') {
				throw new ValidationError({
					homepage: { messages: ['The example homepage is not allowed.'] },
				});
			}

			console.log(Object.fromEntries(formData.entries()));

			return true;
		},
		render: form => {
			form.api
				.getFormEl()
				?.querySelector('button[type="submit"]')
				?.setAttribute('aria-disabled', form.api.isSubmitting ? 'true' : 'false');
		},
	});

	form.init();
});
