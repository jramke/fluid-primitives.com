import { mount } from 'fluid-primitives';
import { Popover } from 'fluid-primitives/popover';
import { ToggleGroup } from 'fluid-primitives/toggle-group';

const popover = mount('primitives:popover', 'placement-popover', ({ props }) =>
    new Popover(props).init()
);

mount('ui:toggleGroup', 'placement-group', ({ props }) =>
    new ToggleGroup({
        ...props,
        onValueChange: ({ value }) =>
            popover?.updateProps({
                positioning: {
                    gutter: 8,
                    placement: value[0] as 'top' | 'right' | 'bottom' | 'left',
                },
            }),
    }).init()
);
