import { mount, mountAll } from 'fluid-primitives';
import { Form } from 'fluid-primitives/form';
import { z } from 'zod';

// Every guest row's own name/email fields are already validated live by the Field/Input
// primitives nested inside FieldArray - Zod just describes what "valid" means per row. Its own
// issue path (e.g. ['guests', 0, 'name']) is matched back to the exact field name Field itself
// renders (`guests[0][name]`) automatically, however many rows currently exist (added or removed
// client-side) - no fixed field list needed.
const guestListSchema = z.object({
    guests: z.array(
        z.object({
            name: z.string().min(1, 'Name is required.'),
            email: z.email('A valid email address is required.'),
        })
    ),
});

mountAll('ui:guestList', ({ props }) => {
    mount('ui:form', props.id + '-form', ({ props }) =>
        new Form({
            ...props,
            validation: guestListSchema,
            onSubmit: async ({ values }) => {
                alert(JSON.stringify(values.toObject()));
                return true;
            },
        }).init()
    );
});
