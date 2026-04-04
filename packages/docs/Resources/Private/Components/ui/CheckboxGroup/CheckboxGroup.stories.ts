import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
	component: await fetchComponent('ui:checkboxGroup.examples.all'),
} satisfies Meta;

export const Simple: StoryObj = {
	args: {
		example_id: 'simple',
	},
};
export const DefaultChecked: StoryObj = {
	args: {
		example_id: 'default-checked',
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
export const MaxSelected: StoryObj = {
	args: {
		example_id: 'max-selected',
	},
};
export const SelectAll: StoryObj = {
	args: {
		example_id: 'select-all',
	},
};
