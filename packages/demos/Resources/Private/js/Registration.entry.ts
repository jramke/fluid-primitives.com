import { extbase, mount } from 'fluid-primitives';
import { Button } from 'fluid-primitives/button';
import { Form } from 'fluid-primitives/form';

const submitButton = mount('ui:button', 'registration-submit', ({ props }) =>
    new Button(props).init()
);

// The login page fills in the email of the new account, see Login.entry.ts
const NEW_ACCOUNT_KEY = 'demos-new-account';

mount('ui:form', 'registration-form', ({ props }) =>
    new Form({
        ...props,
        // The same rules as the server, which checks them again
        validation: ({ values }) => {
            const errors: Record<string, { messages: string[] }> = {};
            const password = values.get('password');
            const confirmation = values.get('passwordConfirmation');
            if (confirmation !== null && confirmation !== password) {
                errors.passwordConfirmation = { messages: ['The passwords do not match.'] };
            }
            return errors;
        },
        onSubmit: async ({ api, values }) => {
            const response = await extbase.post(api.getAction(), values.toObject(), {
                prefix: api.getFieldNamePrefix(),
                objectName: api.getObjectName(),
            });

            const data: { message?: string; username?: string } = await response
                .json()
                .catch(() => ({}));
            if (!response.ok) {
                api.setErrorText(data.message ?? null);
                return false;
            }

            api.setSuccessText(data.message ?? null);
            try {
                if (data.username) sessionStorage.setItem(NEW_ACCOUNT_KEY, data.username);
            } catch {
                // No storage: the login form stays empty
            }
            return true;
        },
        render: form => submitButton?.api.setLoading(form.api.isSubmitting),
    }).init()
);
