import { mountAll } from 'fluid-primitives';
import { Select } from 'fluid-primitives/select';
import { withTiming } from '../../Shared/benchTiming';

// This same asset is loaded by both the "wrapped" scenario (bench:select, via this package's own
// spreadProps wrapper) and the "direct" scenario (primitives:select, the raw primitive rendered
// with no wrapper at all) - mounting both namespaces is harmless, since only whichever one a given
// scenario page actually rendered ever has hydration data to find.
withTiming('select', () => {
    mountAll('bench:select', ({ props }) => {
        const select = new Select(props);
        select.init();
        return select;
    });
    mountAll('primitives:select', ({ props }) => {
        const select = new Select(props);
        select.init();
        return select;
    });
});
