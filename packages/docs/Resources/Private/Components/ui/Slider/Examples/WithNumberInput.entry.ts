import { getHydrationData } from 'fluid-primitives';
import { NumberInput } from 'fluid-primitives/number-input';
import { Slider } from 'fluid-primitives/slider';

(() => {
    const sliderData = getHydrationData('slider', 'synced-slider');
    const numberInputData = getHydrationData('numberInput', 'synced-number-input');

    if (!sliderData || !numberInputData) {
        console.error('Missing hydration data for slider + number input sync example');
        return;
    }

    let currentValue = numberInputData.props.defaultValue;

    function setValue(next: number) {
        if (next === currentValue) return;
        currentValue = next;
        slider.api.setValue([next]);
        numberInput.api.setValue(next);
    }

    const slider = new Slider({
        ...sliderData.props,
        onValueChange: ({ value }) => setValue(value[0]),
    });

    const numberInput = new NumberInput({
        ...numberInputData.props,
        onValueChange: ({ valueAsNumber }) => {
            if (!Number.isNaN(valueAsNumber)) setValue(valueAsNumber);
        },
    });

    slider.init();
    numberInput.init();
})();
