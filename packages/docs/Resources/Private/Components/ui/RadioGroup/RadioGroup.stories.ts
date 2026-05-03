import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
	component: await fetchComponent('ui:radioGroup.examples.all'),
} satisfies Meta;

export const Simple: StoryObj = {
	args: {
		example_id: 'simple',
	},
};
export const DisabledGroup: StoryObj = {
	args: {
		example_id: 'disabled-group',
	},
};
export const DisabledItems: StoryObj = {
	args: {
		example_id: 'disabled-items',
	},
};
export const NoDefault: StoryObj = {
	args: {
		example_id: 'no-default',
	},
};
