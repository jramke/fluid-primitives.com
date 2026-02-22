import { getHydrationData, mount } from 'fluid-primitives';
import { Form, ValidationError } from 'fluid-primitives/form';

mount('form-example', () => {
	const data = getHydrationData('form', 'example-form');
	if (!data) return;

	const form = new Form({
		...data.props,
		onSubmit: async ({ formData, api }) => {
			await new Promise(resolve => setTimeout(resolve, 800));

			const formEl = api.getFormEl();
			const urlInput = formEl?.querySelector<HTMLInputElement>('input[name="homepage"]');
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
