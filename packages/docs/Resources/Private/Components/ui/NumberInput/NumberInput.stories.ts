import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
	component: await fetchComponent('ui:numberInput.examples.all'),
} satisfies Meta;

export const Simple: StoryObj = {
	args: {
		example_id: 'simple',
	},
};
export const MinMax: StoryObj = {
	args: {
		example_id: 'min-max',
	},
};
export const FormatOptions: StoryObj = {
	args: {
		example_id: 'format-options',
	},
};
export const MouseWheel: StoryObj = {
	args: {
		example_id: 'mouse-wheel',
	},
};
export const WithScrubber: StoryObj = {
	args: {
		example_id: 'with-scrubber',
	},
};
