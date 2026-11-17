import { mount } from 'fluid-primitives';
import { Field } from 'fluid-primitives/field';

// The server checks it again, see RegistrationValidator.php
mount('ui:field', 'password-confirmation', ({ props }) =>
    new Field({
        ...props,
        validate: ({ value, formValues }) =>
            value === formValues.password ? null : 'The passwords do not match.',
    }).init()
);
