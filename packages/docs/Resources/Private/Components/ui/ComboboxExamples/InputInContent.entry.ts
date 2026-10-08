import type { InputValueChangeDetails } from '@zag-js/combobox';
import { createFilter } from '@zag-js/i18n-utils';
import { getGlobal, mount } from 'fluid-primitives';
import { Combobox } from 'fluid-primitives/combobox';

const filter = createFilter({ sensitivity: 'base', locale: getGlobal('locale') });

mount('ui:combobox', 'input-in-content', ({ props }) => {
    const combobox = new Combobox({
        ...props,
        // The trigger shows the selection, so the input starts empty every time the popup opens.
        onOpenChange: ({ open }) => {
            if (!open) combobox.api.setInputValue('');
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

    // The input lives in the popup, so the trigger has to show the selection itself.
    const valueTextEl = combobox.hydrator.query('valueText');
    if (valueTextEl) {
        const placeholder = valueTextEl.textContent ?? '';
        combobox.machine.subscribe(() => {
            const { valueAsString } = combobox.api;
            valueTextEl.textContent = valueAsString || placeholder;
            valueTextEl.classList.toggle('text-muted-foreground', !valueAsString);
        });
    }

    return combobox;
});
