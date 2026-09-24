import { mountAll } from 'fluid-primitives';
import { Checkbox } from 'fluid-primitives/checkbox';
import { withTiming } from '../../Shared/benchTiming';

// This same asset is loaded by both the "wrapped" scenario (bench:checkbox, via this package's
// own spreadProps wrapper) and the "direct" scenario (primitives:checkbox, the raw primitive
// rendered with no wrapper at all) - mounting both namespaces is harmless, since only whichever
// one a given scenario page actually rendered ever has hydration data to find.
withTiming('checkbox', () => {
    mountAll('bench:checkbox', ({ props }) => {
        const checkbox = new Checkbox(props);
        checkbox.init();
        return checkbox;
    });
    mountAll('primitives:checkbox', ({ props }) => {
        const checkbox = new Checkbox(props);
        checkbox.init();
        return checkbox;
    });
});
