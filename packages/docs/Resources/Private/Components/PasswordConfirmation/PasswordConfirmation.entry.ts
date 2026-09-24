import { getHydrationData, mountAll } from 'fluid-primitives';
import { Form } from 'fluid-primitives/form';

// A manual callback rather than a single Zod schema with `.refine()` - a `.refine()` only runs
// once the base object shape has already validated, so a too-short `password` would silently
// skip the cross-field "do they match" check entirely (passwordConfirm would never get an error
// while the user is still typing a short password). Checking each independently means
// passwordConfirm's mismatch error can surface regardless of whether password also satisfies its
// own length requirement yet. listenTo on the passwordConfirm field (see the template) does all
// the cross-field re-triggering here - this callback only needs to describe what "valid" means.
mountAll('ui:passwordConfirmation', ({ props }) => {
    const data = getHydrationData('ui:form', props.id + '-form');
    if (!data) return;

    const form = new Form({
        ...data.props,
        validation: ({ values }) => {
            const errors: Record<string, { messages: string[] }> = {};

            const password = values.get('password');
            if (typeof password !== 'string' || password.length < 8) {
                errors.password = { messages: ['Password must be at least 8 characters'] };
            }

            const passwordConfirm = values.get('passwordConfirm');
            if (passwordConfirm !== null && passwordConfirm !== password) {
                errors.passwordConfirm = { messages: ['Passwords do not match'] };
            }

            return errors;
        },
        onSubmit: async ({ values }) => {
            alert(JSON.stringify(values.toObject()));
            return true;
        },
    });

    form.init();
});
