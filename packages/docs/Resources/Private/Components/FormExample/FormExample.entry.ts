import { extbase, mount, mountAll } from 'fluid-primitives';
import { Button } from 'fluid-primitives/button';
import { Form } from 'fluid-primitives/form';

mountAll('ui:formExample', () => {
    const submitButton = mount('ui:button', 'example-form-submit', ({ props }) =>
        new Button(props).init()
    );

    mount('ui:form', 'example-form', ({ props }) =>
        new Form({
            ...props,
            validation: ({ values }) => {
                const homepage = values.get('homepage');
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
            onSubmit: async ({ api, values }) => {
                const [response] = await Promise.all([
                    extbase.post(api.getAction(), values.toObject(), {
                        prefix: api.getFieldNamePrefix(),
                        objectName: api.getObjectName(),
                    }),
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
            render: form => submitButton?.api.setLoading(form.api.isSubmitting),
        }).init()
    );
});
