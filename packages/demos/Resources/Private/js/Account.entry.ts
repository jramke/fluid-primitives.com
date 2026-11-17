import { extbase, mount } from 'fluid-primitives';
import { Form } from 'fluid-primitives/form';

mount('ui:form', 'account-form', ({ props }) =>
    new Form({
        ...props,
        onSubmit: async ({ api, values }) => {
            const response = await extbase.post(api.getAction(), values.toObject(), {
                prefix: api.getFieldNamePrefix(),
                objectName: api.getObjectName(),
            });

            const data: { message?: string; name?: string; initials?: string } = await response
                .json()
                .catch(() => ({}));
            if (!response.ok) {
                api.setErrorText(data.message ?? null);
                return false;
            }

            // "Edit again" resets the form to the saved name
            const input = document.querySelector<HTMLInputElement>(
                '[data-form-root="account-form"] [data-name="name"] input'
            );
            if (input && data.name) input.defaultValue = data.name;

            // The site header on this page listens for it
            document.dispatchEvent(new CustomEvent('demos:account-updated', { detail: data }));
            api.setSuccessText(data.message ?? null);
            return true;
        },
    }).init()
);
