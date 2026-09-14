import { mountAll } from 'fluid-primitives';
import { NumberInput } from 'fluid-primitives/number-input';

mountAll('number-input', ({ props }) => {
    const numberInput = new NumberInput(props);
    numberInput.init();
    return numberInput;
});
