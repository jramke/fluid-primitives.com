import { getHydrationData, mount } from 'fluid-primitives';
import { Form } from 'fluid-primitives/form';
import { z } from 'zod';

mount('event-registration', ({ props, createHydrator }) => {
	const data = getHydrationData('form', props.id + '-form');
	if (!data) return;

	const hydrator = createHydrator();

	const schema = z.object({
		ticketType: z.enum(['vip', 'standard', 'student'], 'Please select a ticket type'),
		ticketCount: z.coerce
			.number('Please enter a valid number of tickets')
			.min(1, 'You must register at least 1 ticket')
			.max(10, 'You can only register up to 10 tickets'),
		name: z.string('Please enter your name').min(1, 'Please enter your name'),
		email: z.email('Please enter your email'),
		phone: z.string().optional(),
		mode: z.enum(['person', 'virtual'], 'Please select a mode of attendance'),
		a11yNeeds: z.array(z.string()).optional(),
		comment: z.string().optional(),
		privacy: z.literal('1', 'You must agree to the privacy policy'),
	});

	const form = new Form({
		...data.props,
		schema,
		onSubmit: async ({ formData, api, post }) => {
			// Modify formData if needed before sending
			console.log(Object.fromEntries(formData.entries()));

			// Wait at least 800ms so we dont flash a loading state and show were working very hard
			const [response] = await Promise.all([
				post(api.getAction(), formData),
				new Promise(resolve => setTimeout(resolve, 800)),
			]);

			const data = await response.json();

			if (!response.ok) {
				const errorMessageEl = hydrator.getElement('error-message');
				if (errorMessageEl) {
					errorMessageEl.textContent =
						data.message ||
						'There was an error submitting your registration. Please try again.';
				}
			}

			return response.ok;
		},
		render: form => {
			const submitButton = hydrator.getElement('submit-button');
			if (submitButton) {
				if (form.api.isSubmitting) {
					submitButton.setAttribute('aria-disabled', 'true');
					submitButton.textContent = 'Submitting...';
				} else {
					submitButton.setAttribute('aria-disabled', 'false');
					submitButton.textContent = 'Submit';
				}
			}

			// ...
		},
	});

	const retryButton = hydrator.getElement('retry-button');
	if (retryButton) {
		retryButton.addEventListener('click', () => {
			form.api.reset();
		});
	}

	form.init();
});
