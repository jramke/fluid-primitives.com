import { getHydrationData, mountAll } from 'fluid-primitives';
import { Textarea } from 'fluid-primitives/textarea';

mountAll('textarea-transform-example', () => {
    const data = getHydrationData('textarea', 'textarea-transform-example');
    if (!data) return;

    const textarea = new Textarea({
        ...data.props,
        transform: value => value.toUpperCase(),
    });

    textarea.init();
    return textarea;
});
