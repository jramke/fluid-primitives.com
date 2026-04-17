import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
	component: await fetchComponent('ui:switch.examples.all'),
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

export const Disabled: StoryObj = {
	args: {
		example_id: 'disabled',
	},
};

export const WithField: StoryObj = {
	args: {
		example_id: 'with-field',
	},
};
