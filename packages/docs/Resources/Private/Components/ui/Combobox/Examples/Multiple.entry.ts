import type { InputValueChangeDetails } from '@zag-js/combobox';
import { createFilter } from '@zag-js/i18n-utils';
import { getGlobal, mount, Template } from 'fluid-primitives';
import { Combobox } from 'fluid-primitives/combobox';

const filter = createFilter({ sensitivity: 'base', locale: getGlobal('locale') });

mount('combobox', 'multiple-example', ({ props }) => {
    let combobox: Combobox;

    function updateSelectedValues(values: string[]) {
        const wrapper = combobox.getElement('selectedValuesWrapper');
        const emptyValuesText = combobox.getElement('emptyValuesText');

        if (!wrapper) return;

        if (values.length === 0) {
            emptyValuesText?.removeAttribute('hidden');
        } else {
            emptyValuesText?.setAttribute('hidden', '');
        }

        const existingItems = Array.from(wrapper.children);
        let existingValues = [];

        for (const existingItem of existingItems) {
            const value = existingItem.getAttribute('data-value');
            if (!value || values.includes(value)) {
                existingValues.push(value);
                continue;
            }
            existingItem.remove();
        }

        for (const value of values) {
            if (existingValues.includes(value)) continue;
            const instance = new Template(combobox.hydrator!, 'selectedValue');
            instance.root.textContent = value;
            instance.root.setAttribute('data-value', value);
            wrapper.appendChild(instance.root);
        }
    }

    combobox = new Combobox({
        // `collection` is the raw wire shape (ListCollectionData) here - Combobox's own
        // transformProps() turns it into a real ListCollection before the machine sees it, but the
        // constructor's own Props type (unchanged by the generated hydration type) still expects
        // the already-transformed shape statically.
        ...(props as unknown as ConstructorParameters<typeof Combobox>[0]),
        onValueChange: details => {
            updateSelectedValues(details.value);
        },
        onInputValueChange: (details: InputValueChangeDetails) => {
            const source = combobox.getSourceCollection();

            if (details.reason !== 'input-change') {
                combobox.updateProps({ collection: source });
                return;
            }

            const query = details.inputValue.trim();
            combobox.updateProps({
                collection: query
                    ? source.filter(itemString => filter.contains(itemString, query))
                    : source,
            });
        },
    });

    combobox.init();
    return combobox;
});
