import { getHydrationData, mount } from 'fluid-primitives';
import { Form } from 'fluid-primitives/form';
import { z } from 'zod';

mount('event-registration', ({ props, createHydrator }) => {
    const data = getHydrationData('form', props.id + '-form');
    if (!data) return;

    const hydrator = createHydrator();

    const schema = z.object({
        ticketType: z.enum(['vip', 'standard', 'student'], 'Please select a ticket type'),
        ticketCount: z.coerce
            .number('Please enter a valid number of tickets')
            .min(1, 'You must register at least 1 ticket')
            .max(10, 'You can only register up to 10 tickets'),
        name: z.string('Please enter your name').min(1, 'Please enter your name'),
        email: z.email('Please enter your email'),
        phone: z.string().optional(),
        mode: z.enum(['person', 'virtual'], 'Please select a mode of attendance'),
        studentId: z.string().optional(),
        a11yNeeds: z.array(z.string()).optional(),
        comment: z.string().optional(),
        privacy: z.literal('1', 'You must agree to the privacy policy'),
    });

    const needsStudentId = (formData: FormData) => formData.get('ticketType') === 'student';

    const form = new Form({
        ...data.props,
        // We need to manually validate the schema because of the conditional logic for studentId.
        // Zod's refine or discriminatedUnion would result in inconsistent validation while the user interacts with the form
        // otherwise we could just pass the schema instead of the callback
        validation: ({ formData, validateWithStandardSchema }) => {
            let errors = validateWithStandardSchema(schema, formData);
            console.log('validation', { errors, studentId: formData.get('studentId') });

            const hasStudentId =
                formData.get('studentId') !== null && formData.get('studentId') !== '';

            if (needsStudentId(formData) && !hasStudentId) {
                errors = {
                    ...errors,
                    studentId: {
                        messages: ['You need to provide a student id for the student ticket'],
                    },
                };
            }

            return errors;
        },
        onSubmit: async ({ formData, api, post }) => {
            // Modify formData if needed before sending
            console.log(Object.fromEntries(formData.entries()));

            // Wait at least 800ms so we dont flash a loading state and show were working very hard
            const [response] = await Promise.all([
                post(api.getAction(), formData),
                new Promise(resolve => setTimeout(resolve, 800)),
            ]);

            const data = await response.json();

            if (!response.ok) {
                api.setErrorText(
                    data.message ||
                        'There was an error submitting your registration. Please try again.'
                );
                return false;
            }

            api.setSuccessText(
                data.message || 'Your registration was submitted successfully. Thank you.'
            );

            return true;
        },
        render: form => {
            // Conditionally hide and show the studentId field based on the ticket type
            // Note we also disable the field, so its omitted by FormData and therefore not validated or passed to the server
            const studentIdField = form.api.getField('studentId')!;
            studentIdField.getRootEl()!.hidden = !needsStudentId(form.api.getValues());
            studentIdField.setDisabled(!needsStudentId(form.api.getValues()));

            // Update submit button based on form state
            const submitButton = hydrator.getElement('submit-button');
            if (submitButton) {
                if (form.api.isSubmitting) {
                    submitButton.setAttribute('aria-disabled', 'true');
                    submitButton.textContent = 'Submitting...';
                } else {
                    submitButton.setAttribute('aria-disabled', 'false');
                    submitButton.textContent = 'Submit';
                }
            }

            // ...
        },
    });

    form.init();
});
