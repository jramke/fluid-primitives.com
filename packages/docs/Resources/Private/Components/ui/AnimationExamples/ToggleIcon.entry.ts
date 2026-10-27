import { mountAll } from 'fluid-primitives';
import { Toggle } from 'fluid-primitives/toggle';

mountAll('primitives:toggle', ({ props }) => new Toggle(props).init());
