import { mount, Template } from 'fluid-primitives';
import { Combobox } from 'fluid-primitives/combobox';

mount('combobox', 'multiple-example', ({ props, controlled }) => {
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
        ...props,
        controlled,
        onValueChange: details => {
            updateSelectedValues(details.value);
        },
    });

    combobox.init();
    return combobox;
});
