import { mount } from 'fluid-primitives';
import { Form } from 'fluid-primitives/form';

// The demo has no endpoint, so the submit stays on the page and shows the success state
mount('ui:form', 'field-native-validation', ({ props }) =>
    new Form({
        ...props,
        onSubmit: async () => true,
    }).init()
);
