import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
    component: await fetchComponent('ui:datePicker.examples.all'),
} satisfies Meta;

export const Simple: StoryObj = {
    args: {
        example_id: 'simple',
    },
};
export const WithField: StoryObj = {
    args: {
        example_id: 'with-field',
    },
};
export const Range: StoryObj = {
    args: {
        example_id: 'range',
    },
};
export const Multiple: StoryObj = {
    args: {
        example_id: 'multiple',
    },
};
export const MinMax: StoryObj = {
    args: {
        example_id: 'min-max',
    },
};
export const Inline: StoryObj = {
    args: {
        example_id: 'inline',
    },
};
