import { mountAll } from 'fluid-primitives';
import { Popover } from 'fluid-primitives/popover';

mountAll('popover', ({ props }) => {
    const popover = new Popover(props);
    popover.init();
    return popover;
});
