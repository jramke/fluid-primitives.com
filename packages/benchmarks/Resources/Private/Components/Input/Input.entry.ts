import { mountAll } from 'fluid-primitives';
import { Input } from 'fluid-primitives/input';
import { withTiming } from '../../Shared/benchTiming';

withTiming('input', () => {
    mountAll('input', ({ props }) => {
        const input = new Input(props);
        input.init();
        return input;
    });
});
