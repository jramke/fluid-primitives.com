import { mount } from 'fluid-primitives';
import { Form } from 'fluid-primitives/form';

// The demo has no endpoint, so the submit stays on the page and shows the success state
mount('ui:form', 'book-a-table', ({ props }) =>
    new Form({
        ...props,
        onSubmit: async ({ api, values }) => {
            const guests = Number(values.get('guests'));
            api.setSuccessText(
                `Your table for ${guests} ${guests === 1 ? 'guest' : 'guests'} is booked.`
            );
            return true;
        },
    }).init()
);
