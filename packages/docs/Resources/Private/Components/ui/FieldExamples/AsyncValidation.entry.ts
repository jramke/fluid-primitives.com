import { mount, mountAll } from 'fluid-primitives';
import { Field } from 'fluid-primitives/field';

const taken = ['admin', 'root', 'ada'];

mountAll('ui:fieldExamples.asyncValidation', () => {
    mount('ui:field', 'username-availability', ({ props }) => {
        const field = new Field({
            ...props,
            validate: async ({ value, validity }) => {
                // only ask once the browser's own checks (required, minlength, ...) pass
                if (!validity.valid) return null;

                // stands in for a request, e.g. `extbase.get(...)`
                await new Promise(resolve => setTimeout(resolve, 700));
                return taken.includes(value.toLowerCase()) ? `"${value}" is already taken.` : null;
            },
        });
        field.init();
        return field;
    });
});
