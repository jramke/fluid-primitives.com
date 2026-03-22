import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
	component: await fetchComponent('ui:popover.examples.all'),
} satisfies Meta;

export const Simple: StoryObj = {
	args: {
		example_id: 'simple',
	},
};
export const Modal: StoryObj = {
	args: {
		example_id: 'modal',
	},
};
export const CustomPositioning: StoryObj = {
	args: {
		example_id: 'custom-positioning',
	},
};
export const WithCloseButton: StoryObj = {
	args: {
		example_id: 'with-close-button',
	},
};
