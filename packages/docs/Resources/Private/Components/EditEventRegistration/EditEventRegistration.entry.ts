import { getHydrationData, mountAll } from 'fluid-primitives';
import { Form, type FormValues } from 'fluid-primitives/form';
import { z } from 'zod';

mountAll('editEventRegistration', ({ props, createHydrator }) => {
    const data = getHydrationData('form', props.id + '-form');
    if (!data) return;

    const hydrator = createHydrator();

    const schema = z.object({
        ticketType: z.enum(['vip', 'standard', 'student'], 'Please select a ticket type'),
        ticketCount: z.coerce
            .number('Please enter a valid number of tickets')
            .min(1, 'You must register at least 1 ticket')
            .max(10, 'You can only register up to 10 tickets'),
        person: z.object({
            name: z.string('Please enter your name').min(1, 'Please enter your name'),
            email: z.email('Please enter your email'),
            phone: z.string().optional(),
            country: z.string('Please select your country').min(1, 'Please select your country'),
        }),
        mode: z.enum(['person', 'virtual'], 'Please select a mode of attendance'),
        studentId: z.string().optional(),
        a11yNeeds: z.array(z.string()).optional(),
        comment: z.string().optional(),
        privacy: z.literal('1', 'You must agree to the privacy policy'),
    });

    const needsStudentId = (values: FormValues) => values.get('ticketType') === 'student';

    const form = new Form({
        ...data.props,
        // See EventRegistration.entry.ts for why this is a callback instead of just the schema.
        validation: ({ values, validateWithStandardSchema }) => {
            let errors = validateWithStandardSchema(schema);

            const hasStudentId = values.get('studentId') !== null && values.get('studentId') !== '';

            if (needsStudentId(values) && !hasStudentId) {
                errors = {
                    ...errors,
                    studentId: {
                        messages: ['You need to provide a student id for the student ticket'],
                    },
                };
            }

            return errors;
        },
        onSubmit: async ({ api, post }) => {
            const [response] = await Promise.all([
                post(api.getAction()),
                new Promise(resolve => setTimeout(resolve, 800)),
            ]);

            const data = await response.json();

            if (!response.ok) {
                api.setErrorText(
                    data.message || 'There was an error saving this registration. Please try again.'
                );
                return false;
            }

            api.setSuccessText(data.message || 'The registration was updated successfully.');

            return true;
        },
        render: form => {
            // Conditionally hide and show the studentId field based on the ticket type.
            // Note we also disable it, so it's omitted by FormData and therefore not validated or passed to the server.
            const showStudentFields = needsStudentId(form.api.getValues());

            const studentIdField = form.api.getField('studentId')!;
            studentIdField.getRootEl()!.hidden = !showStudentFields;
            studentIdField.setDisabled(!showStudentFields);

            const submitButton = hydrator.getElement('submit-button');
            if (submitButton) {
                if (form.api.isSubmitting) {
                    submitButton.setAttribute('aria-disabled', 'true');
                    submitButton.textContent = 'Saving...';
                } else {
                    submitButton.setAttribute('aria-disabled', 'false');
                    submitButton.textContent = 'Save changes';
                }
            }
        },
    });

    form.init();
});
