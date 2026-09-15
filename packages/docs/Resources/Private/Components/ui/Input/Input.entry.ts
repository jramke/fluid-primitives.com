import { mountAll } from 'fluid-primitives';
import { Input } from 'fluid-primitives/input';

mountAll('input', ({ props }) => {
    const input = new Input(props);
    input.init();
    return input;
});
