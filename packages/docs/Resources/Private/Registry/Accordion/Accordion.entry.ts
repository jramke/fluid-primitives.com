import { mountAll } from 'fluid-primitives';
import { Accordion } from 'fluid-primitives/accordion';

mountAll('accordion', ({ props }) => {
    const accordion = new Accordion(props);
    accordion.init();
    return accordion;
});
