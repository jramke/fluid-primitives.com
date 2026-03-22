import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
	component: await fetchComponent('ui:select.examples.all'),
} satisfies Meta;

export const Simple: StoryObj = {
	args: {
		example_id: 'simple',
	},
};
export const DefaultValue: StoryObj = {
	args: {
		example_id: 'default-value',
	},
};
export const WithGroups: StoryObj = {
	args: {
		example_id: 'with-groups',
	},
};
export const WithField: StoryObj = {
	args: {
		example_id: 'with-field',
	},
};
export const Multiple: StoryObj = {
	args: {
		example_id: 'multiple',
	},
};
export const DisabledItems: StoryObj = {
	args: {
		example_id: 'disabled-items',
	},
};
