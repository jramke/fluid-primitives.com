import { getHydrationData, mount } from 'fluid-primitives';
import { NumberInput } from 'fluid-primitives/number-input';
import { Slider } from 'fluid-primitives/slider';

(() => {
    // NumberInput's own defaultValue is the shared initial value - unlike Slider's own (always
    // normalized to a float[] client-side, one per thumb), it's kept as the bare number this needs.
    let currentValue = getHydrationData('ui:numberInput', 'synced-number-input')?.props
        .defaultValue as number | undefined;

    function setValue(next: number) {
        if (next === currentValue) return;
        currentValue = next;
        slider?.api.setValue([next]);
        numberInput?.api.setValue(next);
    }

    const slider = mount('ui:slider', 'synced-slider', ({ props }) => {
        const instance = new Slider({
            ...props,
            onValueChange: ({ value }) => setValue(value[0]),
        });
        instance.init();
        return instance;
    });

    const numberInput = mount('ui:numberInput', 'synced-number-input', ({ props }) => {
        const instance = new NumberInput({
            ...props,
            onValueChange: ({ valueAsNumber }) => {
                if (!Number.isNaN(valueAsNumber)) setValue(valueAsNumber);
            },
        });
        instance.init();
        return instance;
    });
})();
