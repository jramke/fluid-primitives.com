import { mountAll } from 'fluid-primitives';
import { Field } from 'fluid-primitives/field';
import { FieldArray } from 'fluid-primitives/field-array';
import { Input } from 'fluid-primitives/input';

// A row appended client-side (via api.append()) contains its own nested Field/Input instances
// that aren't hydrated yet - `mountAll` is safe to call more than once (already-mounted instances
// are skipped), so re-running these two on every `itemadded` picks up whatever a row's own
// itemTemplate contains without FieldArray needing to know about Field/Input itself.
function mountRowComponents() {
    mountAll('field', ({ props }) => {
        const field = new Field(props);
        field.init();
        return field;
    });
    mountAll('input', ({ props }) => {
        const input = new Input(props);
        input.init();
        return input;
    });
}

mountAll('fieldArray', ({ props }) => {
    const fieldArray = new FieldArray({ ...props, onItemAdded: mountRowComponents });
    fieldArray.init();
    return fieldArray;
});
