import { mount, mountAll } from 'fluid-primitives';
import { Input } from 'fluid-primitives/input';

mountAll('transform-example', () => {
    mount('input', 'transform-example-input', ({ props }) => {
        const input = new Input({
            ...props,
            transform: value => value.toUpperCase(),
        });
        input.init();
        return input;
    });
});
