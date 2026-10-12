import { mount, mountAll } from 'fluid-primitives';
import { DatePicker } from 'fluid-primitives/date-picker';
import { Field } from 'fluid-primitives/field';
import { FieldArray } from 'fluid-primitives/field-array';
import { Input } from 'fluid-primitives/input';
import { Popover } from 'fluid-primitives/popover';
import { Select } from 'fluid-primitives/select';

// A row added on the client contains components that are not hydrated yet. `mountAll` skips the
// ones that are, so running it for everything a row composes is safe on every added row. The
// popover, the select and the date picker of the row render their content through `ui:portal`:
// the clone moves it to the end of the body before these mount.
function mountRowComponents() {
    mountAll('ui:field', ({ props }) => {
        const field = new Field(props);
        field.init();
        return field;
    });
    mountAll('ui:input', ({ props }) => {
        const input = new Input(props);
        input.init();
        return input;
    });
    mountAll('ui:select', ({ props }) => {
        const select = new Select(props);
        select.init();
        return select;
    });
    mountAll('ui:datePicker', ({ props }) => {
        const datePicker = new DatePicker(props);
        datePicker.init();
        return datePicker;
    });
    mountAll('ui:popover', ({ props }) => {
        const popover = new Popover(props);
        popover.init();
        return popover;
    });
}

mount('ui:fieldArray', 'complex-people', ({ props }) => {
    const fieldArray = new FieldArray({ ...props, onItemAdded: mountRowComponents });
    fieldArray.init();
    return fieldArray;
});
