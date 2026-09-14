import { createFilter } from '@zag-js/i18n-utils';
import type { ComboboxFilterHookDetails } from 'fluid-primitives';
import { mount } from 'fluid-primitives';
import { Combobox } from 'fluid-primitives/combobox';

const startsWithFilter = createFilter({ sensitivity: 'base' });

mount('combobox', 'custom-filter-api', ({ props }) => {
    const combobox = new Combobox(props);
    combobox.setFilter(({ inputValue, collection }: ComboboxFilterHookDetails) => {
        const query = inputValue.trim();

        if (!query) {
            return collection;
        }

        return collection.filter((itemString: string) =>
            startsWithFilter.startsWith(itemString, query)
        );
    });
    combobox.init();
    return combobox;
});
