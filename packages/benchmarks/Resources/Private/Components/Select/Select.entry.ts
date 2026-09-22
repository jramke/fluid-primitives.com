import { mountAll } from 'fluid-primitives';
import { Select } from 'fluid-primitives/select';
import { withTiming } from '../../Shared/benchTiming';

withTiming('select', () => {
    mountAll('select', ({ props }) => {
        // @ts-expect-error
        const select = new Select(props);
        select.init();
        return select;
    });
});
