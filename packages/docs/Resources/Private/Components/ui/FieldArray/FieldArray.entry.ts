import { mountAll } from 'fluid-primitives';
import { Field } from 'fluid-primitives/field';
import { FieldArray, type FieldArrayAnnounceInfo } from 'fluid-primitives/field-array';
import { Input } from 'fluid-primitives/input';

// A row appended client-side (via api.append()) contains its own nested Field/Input instances
// that aren't hydrated yet - `mountAll` is safe to call more than once (already-mounted instances
// are skipped), so re-running these two on every `itemadded` picks up whatever a row's own
// itemTemplate contains without FieldArray needing to know about Field/Input itself.
function mountRowComponents() {
    mountAll('field', ({ props }) => {
        // @ts-expect-error
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
    const serverTranslations = props.translations as
        { rowAdded?: string; rowRemoved?: string } | undefined;

    // @ts-expect-error
    const fieldArray = new FieldArray({
        ...props,
        onItemAdded: mountRowComponents,
        translations: {
            ...serverTranslations,
            // A custom announcement built from the row's own fields, falling back to the
            // translated default (with its `%number%` placeholder) while they're still empty -
            // e.g. right after a row was just added and hasn't been filled in yet.
            rowRemoved: ({ getFieldValue }: FieldArrayAnnounceInfo) => {
                const firstName = getFieldValue('firstName');
                const lastName = getFieldValue('lastName');
                if (!firstName && !lastName) return serverTranslations?.rowRemoved ?? false;

                return `Person ${[firstName, lastName].filter(Boolean).join(' ')} removed.`;
            },
        },
    });
    fieldArray.init();

    return fieldArray;
});
