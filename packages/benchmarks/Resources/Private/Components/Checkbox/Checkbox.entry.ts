import { mountAll } from 'fluid-primitives';
import { Checkbox } from 'fluid-primitives/checkbox';
import { withTiming } from '../../Shared/benchTiming';

withTiming('checkbox', () => {
    mountAll('checkbox', ({ props }) => {
        const checkbox = new Checkbox(props);
        checkbox.init();
        return checkbox;
    });
});
