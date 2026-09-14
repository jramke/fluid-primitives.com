import { mountAll } from 'fluid-primitives';
import { Clipboard } from 'fluid-primitives/clipboard';

mountAll('clipboard', ({ props }) => {
    const clipboard = new Clipboard(props);
    clipboard.init();
    return clipboard;
});
