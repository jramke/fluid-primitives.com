import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
	component: await fetchComponent('ui:alert.simple'),
} satisfies Meta;

export const Pirate: StoryObj = {
	args: {
		title: 'Yar Pirate Ipsum',
	},
};
