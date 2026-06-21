import { getHydrationData, mount } from 'fluid-primitives';
import { Form } from 'fluid-primitives/form';

mount('form-example', () => {
    const data = getHydrationData('form', 'example-form');
    if (!data) return;

    const form = new Form({
        ...data.props,
        validation: ({ formData }) => {
            const homepage = formData.get('homepage');
            if (typeof homepage !== 'string' || homepage.trim() === '') {
                return {
                    homepage: { messages: ['Please enter an url.'] },
                };
            }

            try {
                new URL(homepage);
            } catch {
                return {
                    homepage: { messages: ['Please enter a valid url.'] },
                };
            }

            if (homepage === 'https://example.com') {
                return {
                    homepage: { messages: ['The example homepage is not allowed.'] },
                };
            }
        },
        onSubmit: async ({ formData, api, post }) => {
            const [response] = await Promise.all([
                post(api.getAction(), formData),
                new Promise(resolve => setTimeout(resolve, 800)),
            ]);

            const data = await response.json();

            if (!response.ok) {
                api.setErrorText(data.message || 'The demo server is unavailable right now.');
                return false;
            }

            api.setSuccessText(data.message || 'Your homepage was submitted successfully.');

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
