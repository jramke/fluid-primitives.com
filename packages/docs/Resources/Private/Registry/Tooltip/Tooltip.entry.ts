import { mount } from 'fluid-primitives';
import { Tooltip } from 'fluid-primitives/tooltip';

mount('tooltip', ({ props }) => {
	const tooltip = new Tooltip(props);
	tooltip.init();
	return tooltip;
});
