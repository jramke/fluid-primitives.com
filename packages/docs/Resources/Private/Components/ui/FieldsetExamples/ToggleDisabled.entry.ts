import { mount, mountAll } from 'fluid-primitives';
import { Fieldset } from 'fluid-primitives/fieldset';
import { Switch } from 'fluid-primitives/switch';

mountAll('ui:fieldsetExamples.toggleDisabled', () => {
    const fieldset = mount('ui:fieldset', 'edit-fieldset', ({ props }) => {
        const instance = new Fieldset(props);
        instance.init();
        return instance;
    });

    mount('ui:switch', 'edit-mode', ({ props }) => {
        const instance = new Switch({
            ...props,
            onCheckedChange: ({ checked }) => {
                instance.updateProps({ checked });
                fieldset?.updateProps({ disabled: !checked });
            },
        });
        instance.init();
        return instance;
    });
});
