import { extbase, mount } from 'fluid-primitives';
import { Form } from 'fluid-primitives/form';

mount('ui:form', 'registration', ({ props }) =>
    new Form({
        ...props,
        // HTML has no constraint for a repeated password. The server checks it again, see RegistrationValidator.php
        validation: ({ values }) =>
            values.get('passwordConfirmation') === values.get('password')
                ? null
                : { passwordConfirmation: { messages: ['The passwords do not match.'] } },
        onSubmit: async ({ api, values }) => {
            const response = await extbase.post(api.getAction(), values.toObject(), {
                prefix: api.getFieldNamePrefix(),
                objectName: api.getObjectName(),
            });

            const data: { message?: string } = await response.json().catch(() => ({}));
            if (!response.ok) {
                api.setErrorText(data.message ?? null);
                return false;
            }

            api.setSuccessText(data.message ?? null);
            return true;
        },
    }).init()
);
