import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
	component: await fetchComponent('ui:scrollArea.examples.all'),
} satisfies Meta;

export const Simple: StoryObj = {
	args: {
		example_id: 'simple',
	},
};
export const Horizontal: StoryObj = {
	args: {
		example_id: 'horizontal',
	},
};
export const BothDirections: StoryObj = {
	args: {
		example_id: 'both-directions',
	},
};
