import { mountAll } from 'fluid-primitives';
import { FieldArray, type FieldArrayAnnounceInfo } from 'fluid-primitives/field-array';

mountAll('ui:fieldArray', ({ props }) => {
    const serverTranslations = props.translations as
        { rowAdded?: string; rowRemoved?: string } | undefined;
    const maxItems = props.maxItems as number | undefined;

    const fieldArray = new FieldArray({
        ...props,
        onItemAdded: updateStatusText,
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
    // `query` returns null for every other example, so this is a no-op there. Kept here
    // rather than duplicated per-example since every example already shares this one entry file.
    function updateStatusText() {
        const statusEl = fieldArray.hydrator.query<HTMLElement>('status');
        if (!statusEl || maxItems === undefined) return;

        const count = fieldArray.api.getRows().length;
        statusEl.textContent = `${count} of ${maxItems} added (${maxItems - count} remaining)`;
    }

    fieldArray.init();

    return fieldArray;
});
