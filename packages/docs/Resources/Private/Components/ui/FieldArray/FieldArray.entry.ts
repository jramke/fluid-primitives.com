import { mountAll } from 'fluid-primitives';
import { Field } from 'fluid-primitives/field';
import { FieldArray, type FieldArrayAnnounceInfo } from 'fluid-primitives/field-array';
import { Input } from 'fluid-primitives/input';

// A row appended client-side (via api.append()) contains its own nested Field/Input instances
// that aren't hydrated yet - `mountAll` is safe to call more than once (already-mounted instances
// are skipped), so re-running these two on every `itemadded` picks up whatever a row's own
// itemTemplate contains without FieldArray needing to know about Field/Input itself.
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
}

mountAll('ui:fieldArray', ({ props }) => {
    const serverTranslations = props.translations as
        { rowAdded?: string; rowRemoved?: string } | undefined;
    const maxItems = props.maxItems as number | undefined;

    const fieldArray = new FieldArray({
        ...props,
        onItemAdded: () => {
            mountRowComponents();
            updateStatusText();
        },
        onItemRemoved: updateStatusText,
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

    // Only the "Limiting Row Count" example authors a `status` ref inside its own row markup -
    // `getElement` returns null for every other example, so this is a no-op there. Kept here
    // rather than duplicated per-example since every example already shares this one entry file.
    function updateStatusText() {
        const statusEl = fieldArray.getElement<HTMLElement>('status');
        if (!statusEl || maxItems === undefined) return;

        const count = fieldArray.api.getRows().length;
        statusEl.textContent = `${count} of ${maxItems} added (${maxItems - count} remaining)`;
    }

    fieldArray.init();

    return fieldArray;
});
