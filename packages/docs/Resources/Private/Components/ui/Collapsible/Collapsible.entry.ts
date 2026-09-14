import { mountAll } from 'fluid-primitives';
import { Collapsible } from 'fluid-primitives/collapsible';

mountAll('collapsible', ({ props }) => {
    const collapsible = new Collapsible(props);
    collapsible.init();
    return collapsible;
});
