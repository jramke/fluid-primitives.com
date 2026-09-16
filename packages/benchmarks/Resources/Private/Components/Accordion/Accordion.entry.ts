import { mountAll } from 'fluid-primitives';
import { Accordion } from 'fluid-primitives/accordion';
import { withTiming } from '../../Shared/benchTiming';

withTiming('accordion', () => {
    mountAll('accordion', ({ props }) => {
        const accordion = new Accordion(props);
        accordion.init();
        return accordion;
    });
});
