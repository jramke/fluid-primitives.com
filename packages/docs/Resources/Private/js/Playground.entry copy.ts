import { getHydrationData } from 'fluid-primitives';
import { Form } from 'fluid-primitives/form';

(() => {
	const data = getHydrationData('form', 'test-form');
	if (!data) return;

	const { props } = data;

	const form = new Form({
		...props,
		// schema: v.object({
		// 	something: v.pipe(v.string(), v.minLength(1)),
		// 	// checkboxExample: v.boolean(),
		// 	selectExample: v.string(),
		// }),
		onSubmit: async ({ formData, api, post }) => {
			const response = await post(api.getAction(), formData);
			console.log('FORM SUBMIT RESPONSE', response);

			return new Promise<boolean>(resolve => {
				setTimeout(() => resolve(response.ok), 1000);
			});
		},
		render: form => {
			console.log('custom render function called', {
				errors: form.api.getErrors(),
				values: form.api.getValues(),
				dirty: form.api.getDirty(),
				fields: form.api.getFields().size,
			});

			// TODO: maybe we can make a form.submitButton api
			const submitButton = form.api.getFormEl()?.querySelector('button[type="submit"]') as
				| HTMLButtonElement
				| undefined;
			if (submitButton) {
				submitButton.disabled = form.api.isSubmitting;
			}
		},
		reactiveFields: ['checkboxExample'],
	});
	form.init();
})();
