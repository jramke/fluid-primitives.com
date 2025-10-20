import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
	component: await fetchComponent('ui:clipboard.examples.simple'),
} satisfies Meta;

export const Default: StoryObj = {
	args: {
		// title: 'Yar Pirate Ipsum',
	},
};
