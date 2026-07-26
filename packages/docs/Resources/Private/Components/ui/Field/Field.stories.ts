import { type Meta, type StoryObj, fetchComponent } from '@andersundsehr/storybook-typo3';

export default {
    component: await fetchComponent('ui:field.examples.all'),
} satisfies Meta;

export const Simple: StoryObj = {
    args: {
        example_id: 'simple',
    },
};
export const WithDescription: StoryObj = {
    args: {
        example_id: 'with-description',
    },
};
export const WithCheckbox: StoryObj = {
    args: {
        example_id: 'with-checkbox',
    },
};
export const Required: StoryObj = {
    args: {
        example_id: 'required',
    },
};
export const Invalid: StoryObj = {
    args: {
        example_id: 'invalid',
    },
};
export const Disabled: StoryObj = {
    args: {
        example_id: 'disabled',
    },
};
