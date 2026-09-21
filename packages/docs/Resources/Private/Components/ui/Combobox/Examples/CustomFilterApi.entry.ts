import type { InputValueChangeDetails } from '@zag-js/combobox';
import { createFilter } from '@zag-js/i18n-utils';
import { mount } from 'fluid-primitives';
import { Combobox } from 'fluid-primitives/combobox';

const filter = createFilter({ sensitivity: 'base' });

mount('combobox', 'custom-filter-api', ({ props }) => {
    let combobox: Combobox;

    combobox = new Combobox({
        // `collection` is the raw wire shape (ListCollectionData) here - Combobox's own
        // transformProps() turns it into a real ListCollection before the machine sees it, but the
        // constructor's own Props type (unchanged by the generated hydration type) still expects
        // the already-transformed shape statically.
        ...(props as unknown as ConstructorParameters<typeof Combobox>[0]),
        onInputValueChange: (details: InputValueChangeDetails) => {
            const source = combobox.getSourceCollection();

            if (details.reason !== 'input-change') {
                combobox.updateProps({ collection: source });
                return;
            }

            const query = details.inputValue.trim();
            combobox.updateProps({
                collection: query
                    ? source.filter(itemString => filter.startsWith(itemString, query))
                    : source,
            });
        },
    });

    combobox.init();
    return combobox;
});
