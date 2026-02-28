import { mount } from 'fluid-primitives';
import { ScrollArea } from 'fluid-primitives/scroll-area';

mount('scroll-area', ({ props }) => {
	const scrollArea = new ScrollArea(props);
	scrollArea.init();
	return scrollArea;
});
