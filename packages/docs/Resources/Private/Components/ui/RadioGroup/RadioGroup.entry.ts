import { mountAll } from 'fluid-primitives';
import { RadioGroup } from 'fluid-primitives/radio-group';

mountAll('radioGroup', ({ props }) => {
    const radioGroup = new RadioGroup(props);
    radioGroup.init();
    return radioGroup;
});
