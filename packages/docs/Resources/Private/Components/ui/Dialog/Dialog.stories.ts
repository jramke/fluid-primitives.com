import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
	component: await fetchComponent('ui:dialog.examples.all'),
} satisfies Meta;

export const Simple: StoryObj = {
	args: {
		example_id: 'simple',
	},
};
export const Alert: StoryObj = {
	args: {
		example_id: 'alert',
	},
};
export const PreventCloseEscape: StoryObj = {
	args: {
		example_id: 'prevent-close-escape',
	},
};
export const PreventCloseOutside: StoryObj = {
	args: {
		example_id: 'prevent-close-outside',
	},
};
