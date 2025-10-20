import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
	component: await fetchComponent('ui:tooltip.examples.default'),
} satisfies Meta;

export const Default: StoryObj = {
	args: {
		defaultOpen: false,
	},
};
