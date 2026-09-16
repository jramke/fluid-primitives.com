import { getHydrationData, mountAll } from 'fluid-primitives';
import { Input } from 'fluid-primitives/input';

mountAll('transform-example', () => {
    const data = getHydrationData('input', 'transform-example');
    if (!data) return;

    const input = new Input({
        ...data.props,
        transform: value => value.toUpperCase(),
    });

    input.init();
    return input;
});
