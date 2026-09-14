import { mountAll } from 'fluid-primitives';
import { Combobox } from 'fluid-primitives/combobox';

mountAll('combobox', ({ props }) => {
    const combobox = new Combobox(props);
    combobox.init();
    return combobox;
});
