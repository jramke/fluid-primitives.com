import { mountAll } from 'fluid-primitives';
import { Checkbox } from 'fluid-primitives/checkbox';

mountAll('checkbox', ({ props }) => {
    const checkbox = new Checkbox(props);
    checkbox.init();
    return checkbox;
});
