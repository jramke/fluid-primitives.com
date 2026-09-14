import { mountAll } from 'fluid-primitives';
import { Tabs } from 'fluid-primitives/tabs';

mountAll('tabs', ({ props }) => {
    const tabs = new Tabs(props);
    tabs.init();
    return tabs;
});
