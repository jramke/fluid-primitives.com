import { mountAll } from 'fluid-primitives';
import { ScrollArea } from 'fluid-primitives/scroll-area';

mountAll('scrollArea', ({ props }) => {
    const scrollArea = new ScrollArea(props);
    scrollArea.init();
    return scrollArea;
});
