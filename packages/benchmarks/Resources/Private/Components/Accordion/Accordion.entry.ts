import { mountAll } from 'fluid-primitives';
import { Accordion } from 'fluid-primitives/accordion';
import { withTiming } from '../../Shared/benchTiming';

// This same asset is loaded by both the "wrapped" scenario (bench:accordion, via this package's
// own spreadProps wrapper) and the "direct" scenario (primitives:accordion, the raw primitive
// rendered with no wrapper at all) - mounting both namespaces is harmless, since only whichever
// one a given scenario page actually rendered ever has hydration data to find.
withTiming('accordion', () => {
    mountAll('bench:accordion', ({ props }) => {
        const accordion = new Accordion(props);
        accordion.init();
        return accordion;
    });
    mountAll('primitives:accordion', ({ props }) => {
        const accordion = new Accordion(props);
        accordion.init();
        return accordion;
    });
});
