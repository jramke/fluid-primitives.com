import { getHydrationData, mount } from 'fluid-primitives';
import { Form, ValidationError } from 'fluid-primitives/form';

mount('form-example', () => {
	const data = getHydrationData('form', 'example-form');
	if (!data) return;

	const form = new Form({
		...data.props,
		onSubmit: async ({ formData, api }) => {
			const urlInput = api.getFormControl('homepage');
			if (!urlInput?.checkValidity()) {
				throw new ValidationError({
					homepage: { messages: ['Please enter a valid url.'] },
				});
			}

			if (formData.get('homepage') === 'https://example.com') {
				throw new ValidationError({
					homepage: { messages: ['The example homepage is not allowed.'] },
				});
			}

			await new Promise(resolve => setTimeout(resolve, 800));

			alert(JSON.stringify(api.formDataToObject(), null, 2));

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
