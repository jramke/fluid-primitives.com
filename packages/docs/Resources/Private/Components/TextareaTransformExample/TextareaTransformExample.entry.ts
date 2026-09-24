import { mount, mountAll } from 'fluid-primitives';
import { Textarea } from 'fluid-primitives/textarea';

mountAll('ui:textareaTransformExample', () => {
    mount('ui:textarea', 'transform-example-textarea', ({ props }) => {
        const textarea = new Textarea({
            ...props,
            transform: value => value.toUpperCase(),
        });

        textarea.init();
        return textarea;
    });
});
