import { mount } from 'fluid-primitives';
import { Switch } from 'fluid-primitives/switch';

mount('switch', ({ props }) => {
    const switchInstance = new Switch(props);
    switchInstance.init();
    return switchInstance;
});
