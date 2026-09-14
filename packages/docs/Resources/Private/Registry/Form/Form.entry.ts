import { mountAll } from 'fluid-primitives';
import { Form } from 'fluid-primitives/form';

mountAll('form', ({ props }) => {
    const form = new Form(props);
    form.init();
    return form;
});
