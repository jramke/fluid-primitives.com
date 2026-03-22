import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
	component: await fetchComponent('ui:clipboard.examples.all'),
} satisfies Meta;

export const Simple: StoryObj = {
	args: {
		example_id: 'simple',
	},
};
export const CopyButtonOnly: StoryObj = {
	args: {
		example_id: 'copy-button-only',
	},
};
export const CustomTimeout: StoryObj = {
	args: {
		example_id: 'custom-timeout',
	},
};
