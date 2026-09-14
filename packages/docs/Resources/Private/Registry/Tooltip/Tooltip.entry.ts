import { mountAll } from 'fluid-primitives';
import { Tooltip } from 'fluid-primitives/tooltip';

mountAll('tooltip', ({ props }) => {
    const tooltip = new Tooltip(props);
    tooltip.init();
    return tooltip;
});
