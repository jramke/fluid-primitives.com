import { mount } from 'fluid-primitives';
import { Menu } from 'fluid-primitives/menu';

mount('ui:menu', 'menu-multiple-triggers', ({ props }) => {
    let trigger: string | null = null;

    return new Menu({
        ...props,
        onTriggerValueChange: ({ value }) => (trigger = value),
        onSelect: ({ value }) => alert(`Clicked ${value} of trigger ${trigger}`),
    }).init();
});
