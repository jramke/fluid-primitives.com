import { extbase, mount } from 'fluid-primitives';
import { Button } from 'fluid-primitives/button';
import { Form } from 'fluid-primitives/form';

const submitButton = mount('ui:button', 'registration-submit', ({ props }) =>
    new Button(props).init()
);

// The new account logs in with the felogin form on the same page: fill in its email and move on to the password
function continueWithLogin(username: string) {
    const login = document.getElementById('login-form');
    const user = login?.querySelector<HTMLInputElement>('input[name="user"]');
    if (!user) return;

    user.value = username;
    user.dispatchEvent(new Event('input', { bubbles: true }));
    login?.querySelector<HTMLInputElement>('input[name="pass"]')?.focus();
}

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
            if (data.username) continueWithLogin(data.username);
            return true;
        },
        render: form => submitButton?.api.setLoading(form.api.isSubmitting),
    }).init()
);
