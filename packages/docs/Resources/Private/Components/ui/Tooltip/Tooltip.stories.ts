import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
	component: await fetchComponent('ui:tooltip.examples.all'),
} satisfies Meta;

export const Simple: StoryObj = {
	args: {
		example_id: 'simple',
	},
};
export const IconButton: StoryObj = {
	args: {
		example_id: 'icon-button',
	},
};
export const CustomPositioning: StoryObj = {
	args: {
		example_id: 'custom-positioning',
	},
};
export const OpenDelay: StoryObj = {
	args: {
		example_id: 'open-delay',
	},
};
export const CloseDelay: StoryObj = {
	args: {
		example_id: 'close-delay',
	},
};
export const Disabled: StoryObj = {
	args: {
		example_id: 'disabled',
	},
};
export const MultipleNoDelay: StoryObj = {
	args: {
		example_id: 'multiple-no-delay',
	},
};
