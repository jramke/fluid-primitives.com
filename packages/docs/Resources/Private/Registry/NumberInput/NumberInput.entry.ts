import { mount } from 'fluid-primitives';
import { NumberInput } from 'fluid-primitives/number-input';

mount('number-input', ({ props }) => {
	const numberInput = new NumberInput(props);
	numberInput.init();
	return numberInput;
});
