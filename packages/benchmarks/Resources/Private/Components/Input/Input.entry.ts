import { mountAll } from 'fluid-primitives';
import { Input } from 'fluid-primitives/input';
import { withTiming } from '../../Shared/benchTiming';

// This same asset is loaded by both the "wrapped" scenario (bench:input, via this package's own
// spreadProps wrapper) and the "direct" scenario (primitives:input, the raw primitive rendered
// with no wrapper at all) - mounting both namespaces is harmless, since only whichever one a given
// scenario page actually rendered ever has hydration data to find.
withTiming('input', () => {
    mountAll('bench:input', ({ props }) => {
        const input = new Input(props);
        input.init();
        return input;
    });
    mountAll('primitives:input', ({ props }) => {
        const input = new Input(props);
        input.init();
        return input;
    });
});
