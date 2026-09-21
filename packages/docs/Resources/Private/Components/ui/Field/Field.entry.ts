import { mountAll } from 'fluid-primitives';
import { Field } from 'fluid-primitives/field';

mountAll('field', ({ props }) => {
    const field = new Field(props);
    field.init();
    return field;
});
